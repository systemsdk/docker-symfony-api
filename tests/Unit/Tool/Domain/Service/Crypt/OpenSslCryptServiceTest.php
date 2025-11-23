<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tool\Domain\Service\Crypt;

use App\Tool\Domain\Exception\Crypt\Exception;
use App\Tool\Domain\Service\Crypt\OpenSslCryptService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @package App\Tests
 */
class OpenSslCryptServiceTest extends TestCase
{
    /**
     * @throws Throwable
     */
    #[TestDox('Test that not supported encrypt algorithm will return proper exception.')]
    public function testThatNotSupportedEncryptAlgorithmReturnsException(): void
    {
        $openSslCryptService = new OpenSslCryptService('Mickey Mouse', 'some key');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(OpenSslCryptService::ERROR_WRONG_ALGORITHM);
        $openSslCryptService->encrypt('text for encrypt');
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that missing data for decrypt will return proper exception.')]
    public function testThatMissingDataForDecryptReturnsException(): void
    {
        $openSslCryptService = new OpenSslCryptService('aes-128-gcm', 'some key');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(OpenSslCryptService::ERROR_MISSING_DATA_FOR_DECRYPT);
        $openSslCryptService->decrypt([
            'data' => '',
            'params' => [
                'iv' => '',
                'tag' => '',
            ],
        ]);
    }
}
