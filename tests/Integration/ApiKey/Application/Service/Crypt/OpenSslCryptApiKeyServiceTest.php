<?php

declare(strict_types=1);

namespace App\Tests\Integration\ApiKey\Application\Service\Crypt;

use App\ApiKey\Application\Service\Crypt\Interfaces\OpenSslCryptApiKeyServiceInterface;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Role\Domain\Enum\Role;
use App\User\Domain\Entity\UserGroup;
use App\User\Domain\Repository\Interfaces\UserGroupRepositoryInterface;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

/**
 * @package App\Tests
 */
class OpenSslCryptApiKeyServiceTest extends KernelTestCase
{
    private readonly OpenSslCryptApiKeyServiceInterface $service;
    private readonly ApiKey $apiKeyEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = static::getContainer()->get(OpenSslCryptApiKeyServiceInterface::class);
        $userGroupRepository = static::getContainer()->get(UserGroupRepositoryInterface::class);
        $userGroup = $userGroupRepository->findOneBy([
            'role' => Role::API->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroup);
        $this->apiKeyEntity = new ApiKey()->setDescription('Test description')->addUserGroup($userGroup);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that encryptToken method will not encrypt token with wrong length.')]
    public function testThatEncryptTokenMethodWillNotEncryptTokenWithWrongLength(): void
    {
        // Add extra characters to the token to make its length invalid.
        $token = $this->apiKeyEntity->getToken() . 'test';
        $this->apiKeyEntity->setToken($token);

        $this->service->encryptToken($this->apiKeyEntity);

        // check that the token was not encrypted and other params remains unchanged.
        self::assertSame($token, $this->apiKeyEntity->getToken());
        self::assertNull($this->apiKeyEntity->getTokenParameters());
        self::assertNull($this->apiKeyEntity->getTokenHash());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that decryptToken method will not decrypt token with missing token params or already decrypted.')]
    public function testThatDecryptTokenMethodWillNotDecryptTokenWithMissingTokenParamsOrAlreadyDecrypted(): void
    {
        $token = $this->apiKeyEntity->getToken();

        $this->service->decryptToken($this->apiKeyEntity);

        // Check that the token was not decrypted.
        self::assertSame($token, $this->apiKeyEntity->getToken());
    }
}
