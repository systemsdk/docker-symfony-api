<?php

declare(strict_types=1);

namespace App\Security\Provider;

use App\Entity\ApiKey;
use App\Repository\ApiKeyRepository;
use App\Security\ApiKeyUser;
use App\Security\Provider\Interfaces\ApiKeyUserProviderInterface;
use App\Security\RolesService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiKeyUserProvider
 *
 * @package App\Security\Provider
 */
class ApiKeyUserProvider implements ApiKeyUserProviderInterface, UserProviderInterface
{
    public function __construct(
        private ApiKeyRepository $apiKeyRepository,
        private RolesService $rolesService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return $class === ApiKeyUser::class;
    }

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
        return $this->apiKeyRepository->findOneBy([
            'token' => $token,
        ]);
    }

    /**
     * @reminder Remove this method when Symfony 6.0.0 is released
     *
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function loadUserByUsername(string $username): ApiKeyUser
    {
        return $this->loadUserByIdentifier($username);
    }
}
