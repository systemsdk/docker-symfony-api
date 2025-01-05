<?php

declare(strict_types=1);

namespace App\Tool\Domain\Service\Crypt\Interfaces;

use App\Tool\Domain\Exception\Crypt\Exception;

/**
 * @package App\Tool
 */
interface CryptServiceInterface
{
    /**
     * Method for encrypt text.
     * OpenSsl return value example:
     *     ['data' => 'encrypted text', 'params' => ['iv' => 'hex value', 'tag' => 'hex value']]
     *
     * @throws Exception
     *
     * @return array<string, non-empty-string|array<string, string>|null>
     */
    public function encrypt(string $textForEncrypt): array;

    /**
     * Method for decrypt text.
     * For decrypt OpenSsl we need params array with iv and tag params, example:
     *     ['data' => 'encrypted text', 'params' => ['iv' => 'hex value', 'tag' => 'hex value']]
     *
     * @param array<string, string|array<string, string>|null> $dataForDecrypt
     *
     * @throws Exception
     *
     * @return non-empty-string
     */
    public function decrypt(array $dataForDecrypt): string;
}
