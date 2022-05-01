<?php

declare(strict_types=1);

namespace App\User\Application\Security;

use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityUser
 *
 * @package App\User
 */
class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $identifier;
    private string | null $password;
    private string $language;
    private string $locale;
    private string $timezone;

    /**
     * @var array<int, string>
     */
    private array $roles;

    /**
     * Constructor
     *
     * @param array<int, string> $roles
     */
    public function __construct(User $user, array $roles = [])
    {
        $this->identifier = $user->getId();
        $this->password = $user->getPassword();
        $this->language = $user->getLanguage();
        $this->locale = $user->getLocale();
        $this->timezone = $user->getTimezone();
        $this->roles = $roles;
    }

    public function getUuid(): string
    {
        return $this->getUserIdentifier();
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
        return $this->password;
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

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
