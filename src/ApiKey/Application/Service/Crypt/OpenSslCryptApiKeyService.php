<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Service\Crypt;

use App\ApiKey\Application\Service\Crypt\Interfaces\OpenSslCryptApiKeyServiceInterface;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Tool\Domain\Service\Crypt\Interfaces\OpenSslCryptServiceInterface;

use function strlen;

/**
 * @package App\ApiKey
 */
class OpenSslCryptApiKeyService implements OpenSslCryptApiKeyServiceInterface
{
    public function __construct(
        private readonly OpenSslCryptServiceInterface $openSslCryptService,
        private readonly string $apiKeyTokenHashAlgo,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function encryptToken(ApiKey $apiKey): void
    {
        $token = $apiKey->getToken();

        if (strlen($token) !== ApiKey::TOKEN_LENGTH) {
            return;
        }

        $data = $this->openSslCryptService->encrypt($token);
        $apiKey
            ->setToken($data['data'])
            ->setTokenParameters($data['params'])
            ->setTokenHash(hash($this->apiKeyTokenHashAlgo, $token));
    }

    /**
     * {@inheritdoc}
     */
    public function decryptToken(ApiKey $apiKey): void
    {
        $token = $apiKey->getToken();
        /** @var array{iv: string, tag: string}|null $tokenParams */
        $tokenParams = $apiKey->getTokenParameters();

        if ($tokenParams === null || strlen($token) === ApiKey::TOKEN_LENGTH) {
            return;
        }

        $decryptedToken = $this->openSslCryptService->decrypt([
            'data' => $token,
            'params' => $tokenParams,
        ]);
        $apiKey->setToken($decryptedToken);
    }
}
