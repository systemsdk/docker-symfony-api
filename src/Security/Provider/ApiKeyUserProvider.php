<?php

declare(strict_types=1);

namespace App\Security\Provider;

use App\Entity\ApiKey;
use App\Repository\ApiKeyRepository;
use App\Security\ApiKeyUser;
use App\Security\Interfaces\ApiKeyUserInterface;
use App\Security\Provider\Interfaces\ApiKeyUserProviderInterface;
use App\Security\RolesService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiKeyUserProvider
 *
 * @package App\Security\Provider
 */
class ApiKeyUserProvider implements ApiKeyUserProviderInterface
{
    public function __construct(
        private ApiKeyRepository $apiKeyRepository,
        private RolesService $rolesService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKeyForToken(string $token): ?ApiKey
    {
        return $this->apiKeyRepository->findOneBy(['token' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): ApiKeyUserInterface
    {
        $apiKey = $this->getApiKeyForToken($username);

        if ($apiKey === null) {
            throw new UsernameNotFoundException('API key is not valid');
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
    public function supportsClass($class): bool
    {
        return $class === ApiKeyUser::class;
    }
}
