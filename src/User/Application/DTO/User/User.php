<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as GeneralAppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\Tool\Application\Validator\Constraints as ToolAppAssert;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use App\User\Application\Validator\Constraints as UserAppAssert;
use App\User\Domain\Entity\Interfaces\UserGroupAwareInterface;
use App\User\Domain\Entity\User as Entity;
use App\User\Domain\Entity\UserGroup as UserGroupEntity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

use function array_map;

/**
 * @package App\User
 *
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
#[UserAppAssert\UniqueEmail]
#[UserAppAssert\UniqueUsername]
class User extends RestDto
{
    /**
     * @var array<string, string>
     */
    protected static array $mappings = [
        'password' => 'updatePassword',
        'userGroups' => 'updateUserGroups',
    ];

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $username = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $firstName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    protected string $email = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected Language $language;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected Locale $locale;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[ToolAppAssert\Timezone]
    protected string $timezone = LocalizationServiceInterface::DEFAULT_TIMEZONE;

    /**
     * @var UserGroupEntity[]|array<int, UserGroupEntity>
     */
    #[GeneralAppAssert\EntityReferenceExists(entityClass: UserGroupEntity::class)]
    protected array $userGroups = [];

    #[Assert\NotBlank]
    #[Assert\Length(min: Entity::PASSWORD_MIN_LENGTH, max: 255)]
    protected string $password = '';

    public function __construct()
    {
        $this->language = Language::getDefault();
        $this->locale = Locale::getDefault();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->setVisited('username');
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->setVisited('firstName');
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->setVisited('lastName');
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->setVisited('email');
        $this->email = $email;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->setVisited('language');

        $this->language = $language;

        return $this;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function setLocale(Locale $locale): self
    {
        $this->setVisited('locale');

        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->setVisited('timezone');

        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return array<int, UserGroupEntity>
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param array<int, UserGroupEntity> $userGroups
     */
    public function setUserGroups(array $userGroups): self
    {
        $this->setVisited('userGroups');
        $this->userGroups = $userGroups;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password = null): self
    {
        if ($password !== null) {
            $this->setVisited('password');
            $this->password = $password;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface|Entity $entity
     */
    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->username = $entity->getUsername();
            $this->firstName = $entity->getFirstName();
            $this->lastName = $entity->getLastName();
            $this->email = $entity->getEmail();
            $this->language = $entity->getLanguage();
            $this->locale = $entity->getLocale();
            $this->timezone = $entity->getTimezone();
            /** @var array<int, UserGroupEntity> $groups */
            $groups = $entity->getUserGroups()->toArray();
            $this->userGroups = $groups;
        }

        return $this;
    }

    /**
     * Method to update User entity password.
     */
    protected function updatePassword(Entity $entity, string $value): self
    {
        $entity->setPlainPassword($value);

        return $this;
    }

    /**
     * Method to update User entity user groups.
     *
     * @param array<int, UserGroupEntity> $value
     */
    protected function updateUserGroups(UserGroupAwareInterface $entity, array $value): self
    {
        $entity->clearUserGroups();

        array_map(
            static fn (UserGroupEntity $userGroup): UserGroupAwareInterface => $entity->addUserGroup($userGroup),
            $value,
        );

        return $this;
    }
}
