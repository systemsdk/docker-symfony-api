<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Security\Provider;

use App\ApiKey\Application\Security\ApiKeyUser;
use App\ApiKey\Application\Security\Provider\Interfaces\ApiKeyUserProviderInterface;
use App\ApiKey\Domain\Entity\ApiKey;
use App\ApiKey\Domain\Repository\Interfaces\ApiKeyRepositoryInterface;
use App\Role\Application\Security\RolesService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

/**
 * @package App\ApiKey
 *
 * @template-implements UserProviderInterface<ApiKeyUser>
 */
class ApiKeyUserProvider implements ApiKeyUserProviderInterface, UserProviderInterface
{
    /**
     * @param \App\ApiKey\Infrastructure\Repository\ApiKeyRepository $apiKeyRepository
     */
    public function __construct(
        private readonly bool $apiKeyTokenOpenSslEncrypt,
        private readonly string $apiKeyTokenHashAlgo,
        private readonly ApiKeyRepositoryInterface $apiKeyRepository,
        private readonly RolesService $rolesService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return $class === ApiKeyUser::class;
    }

    /**
     * @throws Throwable
     */
    public function loadUserByIdentifier(string $identifier): ApiKeyUser
    {
        $apiKey = $this->getApiKeyForToken($identifier);

        if ($apiKey === null) {
            throw new UserNotFoundException('API key is not valid');
        }

        return new ApiKeyUser($apiKey, $this->rolesService->getInheritedRoles($apiKey->getRoles()));
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException('API key cannot refresh user');
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKeyForToken(string $token): ?ApiKey
    {
        $searchParams = [
            'token' => $token,
        ];

        if ($this->apiKeyTokenOpenSslEncrypt) {
            $searchParams = [
                'tokenHash' => hash($this->apiKeyTokenHashAlgo, $token),
            ];
        }

        return $this->apiKeyRepository->findOneBy($searchParams);
    }
}
