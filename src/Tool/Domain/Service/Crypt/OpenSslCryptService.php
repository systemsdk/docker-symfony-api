<?php

declare(strict_types=1);

namespace App\Tool\Domain\Service\Crypt;

use App\Tool\Domain\Exception\Crypt\Exception;
use App\Tool\Domain\Service\Crypt\Interfaces\OpenSslCryptServiceInterface;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_string;

/**
 * @package App\Tool
 */
class OpenSslCryptService implements OpenSslCryptServiceInterface
{
    final public const string ERROR_WRONG_ALGORITHM = 'OS does not support crypt algorithm';
    final public const string ERROR_MISSING_DATA_FOR_DECRYPT = 'Missing or wrong data for decrypt';

    public function __construct(
        private readonly string $algorithm,
        private readonly string $openSslKey,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(string $textForEncrypt): array
    {
        $this->checkAlgorithmSupport();
        $ivLen = openssl_cipher_iv_length($this->algorithm);

        if ($ivLen === false) {
            // @codeCoverageIgnoreStart
            throw new Exception('Can not generate ivLen param for crypt');
            // @codeCoverageIgnoreEnd
        }

        $iv = openssl_random_pseudo_bytes($ivLen);
        $tag = null;

        if (!$iv) {
            // @codeCoverageIgnoreStart
            throw new Exception('Can not generate iv param for crypt');
            // @codeCoverageIgnoreEnd
        }

        /** @var false|non-empty-string $encryptedText */
        $encryptedText = openssl_encrypt(
            data: $textForEncrypt,
            cipher_algo: $this->algorithm,
            passphrase: $this->openSslKey,
            iv: $iv,
            tag: $tag,
        );

        if ($encryptedText === false) {
            // @codeCoverageIgnoreStart
            throw new Exception('Can not encrypt data');
            // @codeCoverageIgnoreEnd
        }

        // we need to set decrypt params in order to decrypt it later
        return [
            'data' => $encryptedText,
            'params' => [
                'iv' => bin2hex($iv),
                'tag' => is_string($tag) ? bin2hex($tag) : (string)$tag,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(array $dataForDecrypt): string
    {
        // we need to check if OS supports algorithm for decrypt due to decrypt can be in async mode on another server
        $this->checkAlgorithmSupport();
        $this->checkIfWeHaveDecryptDataAndParams($dataForDecrypt);
        $iv = hex2bin($dataForDecrypt['params']['iv']);
        $tag = is_string($dataForDecrypt['params']['tag'])
            ? hex2bin($dataForDecrypt['params']['tag'])
            : $dataForDecrypt['params']['tag'];

        if (!$iv || !$tag) {
            // @codeCoverageIgnoreStart
            throw new Exception('Can not convert iv or tag param for decrypt');
            // @codeCoverageIgnoreEnd
        }

        /** @var false|non-empty-string $originalText */
        $originalText = openssl_decrypt(
            data: $dataForDecrypt['data'],
            cipher_algo: $this->algorithm,
            passphrase: $this->openSslKey,
            iv: $iv,
            tag: $tag
        );

        if ($originalText === false) {
            // @codeCoverageIgnoreStart
            throw new Exception('Can not decrypt data');
            // @codeCoverageIgnoreEnd
        }

        return $originalText;
    }

    /**
     * @throws Exception
     */
    private function checkAlgorithmSupport(): void
    {
        if (!in_array($this->algorithm, openssl_get_cipher_methods(), true)) {
            throw new Exception(self::ERROR_WRONG_ALGORITHM);
        }
    }

    /**
     * @param array<string, string|array<string, string>|null> $dataForDecrypt
     *
     * @throws Exception
     */
    private function checkIfWeHaveDecryptDataAndParams(array $dataForDecrypt): void
    {
        if (
            !array_key_exists('data', $dataForDecrypt) || $dataForDecrypt['data'] === ''
            || !array_key_exists('params', $dataForDecrypt) || !is_array($dataForDecrypt['params'])
            || !array_key_exists('iv', $dataForDecrypt['params']) || $dataForDecrypt['params']['iv'] === ''
            || !array_key_exists('tag', $dataForDecrypt['params'])
        ) {
            throw new Exception(self::ERROR_MISSING_DATA_FOR_DECRYPT);
        }
    }
}
