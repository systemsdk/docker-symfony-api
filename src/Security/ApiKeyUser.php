<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\ApiKey;
use App\Security\Interfaces\ApiKeyUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_merge;
use function array_unique;

/**
 * Class ApiKeyUser
 *
 * @package App\Security
 */
class ApiKeyUser implements ApiKeyUserInterface, UserInterface
{
    private string $identifier;
    private ApiKey $apiKey;

    /**
     * @var array<int, string>
     */
    private array $roles;

    /**
     * {@inheritdoc}
     */
    public function __construct(ApiKey $apiKey, array $roles)
    {
        $this->apiKey = $apiKey;
        $this->identifier = $this->apiKey->getToken();
        $this->roles = array_unique(array_merge($roles, [RolesService::ROLE_API]));
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey(): ApiKey
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, string> The user roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @remimder Remove this method when Symfony 6.0.0 is released
     *
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
