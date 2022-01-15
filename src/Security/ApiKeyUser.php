<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\ApiKey;
use App\Security\Interfaces\ApiKeyUserInterface;
use App\Security\Interfaces\RolesServiceInterface;
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
    private string $apiKeyIdentifier;

    /**
     * @var array<int, string>
     */
    private array $roles;

    /**
     * {@inheritdoc}
     */
    public function __construct(ApiKey $apiKey, array $roles)
    {
        $this->identifier = $apiKey->getToken();
        $this->apiKeyIdentifier = $apiKey->getId();
        $this->roles = array_unique(array_merge($roles, [RolesServiceInterface::ROLE_API]));
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function getApiKeyIdentifier(): string
    {
        return $this->apiKeyIdentifier;
    }

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
