<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Security;

use App\ApiKey\Application\Security\Interfaces\ApiKeyUserInterface;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Role\Domain\Enum\Role;
use Deprecated;
use Override;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_unique;

/**
 * @package App\ApiKey
 */
class ApiKeyUser implements ApiKeyUserInterface, UserInterface
{
    /**
     * @var non-empty-string
     */
    private readonly string $identifier;
    private readonly string $apiKeyIdentifier;

    /**
     * @var array<int, string>
     */
    private readonly array $roles;

    /**
     * {@inheritdoc}
     */
    public function __construct(ApiKey $apiKey, array $roles)
    {
        $this->identifier = $apiKey->getToken();
        $this->apiKeyIdentifier = $apiKey->getId();
        $this->roles = array_unique([...$roles, Role::API->value]);
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function getApiKeyIdentifier(): string
    {
        return $this->apiKeyIdentifier;
    }

    #[Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
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
    #[Override]
    #[Deprecated]
    public function eraseCredentials(): void
    {
    }
}
