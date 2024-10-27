<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Service\Crypt\Interfaces;

use App\ApiKey\Domain\Entity\ApiKey;
use App\Tool\Domain\Exception\Crypt\Exception;

/**
 * @package App\ApiKey
 */
interface OpenSslCryptApiKeyServiceInterface
{
    /**
     * Method for encrypt ApiKey token.
     *
     * @throws Exception
     */
    public function encryptToken(ApiKey $apiKey): void;

    /**
     * Method for decrypt ApiKey token.
     *
     * @throws Exception
     */
    public function decryptToken(ApiKey $apiKey): void;
}
