<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/User/User.php
 */

namespace App\DTO\User;

use App\DTO\RestDto;
use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use App\Entity\User as Entity;
use App\Entity\UserGroup as UserGroupEntity;
use App\Entity\Interfaces\UserGroupAwareInterface;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @AppAssert\UniqueEmail()
 * @AppAssert\UniqueUsername()
 *
 * @package App\DTO\User
 *
 * @method self|RestDtoInterface  get(string $id): RestDtoInterface
 * @method self|RestDtoInterface  patch(RestDtoInterface $dto): RestDtoInterface
 * @method Entity|EntityInterface update(EntityInterface $entity): EntityInterface
 */
class User extends RestDto
{
    /**
     * @var array
     */
    protected static array $mappings = [
        'password' => 'updatePassword',
        'userGroups' => 'updateUserGroups',
    ];

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255, allowEmptyString="false")
     */
    protected string $username = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255, allowEmptyString="false")
     */
    protected string $firstName = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255, allowEmptyString="false")
     */
    protected string $lastName = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Email()
     */
    protected string $email = '';

    /**
     * @var array|UserGroupEntity[]
     *
     * @AppAssert\EntityReferenceExists(entityClass=UserGroupEntity::class)
     */
    protected array $userGroups = [];

    /**
     * @var string
     *
     * @Assert\Length(min = Entity::PASSWORD_MIN_LENGTH, max = 255, allowEmptyString="false")
     */
    protected string $password = '';


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->setVisited('username');
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->setVisited('firstName');
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->setVisited('lastName');
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->setVisited('email');
        $this->email = $email;

        return $this;
    }

    /**
     * @return UserGroupEntity[]
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param array $userGroups
     *
     * @return User
     */
    public function setUserGroups(array $userGroups): self
    {
        $this->setVisited('userGroups');
        $this->userGroups = $userGroups;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword(?string $password = null): self
    {
        if ($password !== null) {
            $this->setVisited('password');
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Method to load DTO data from specified entity.
     *
     * @param EntityInterface|Entity $entity
     *
     * @return RestDtoInterface|User
     */
    public function load(EntityInterface $entity): RestDtoInterface
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->username = $entity->getUsername();
            $this->firstName = $entity->getFirstName();
            $this->lastName = $entity->getLastName();
            $this->email = $entity->getEmail();
            /** @var array<int, UserGroupEntity> $userGroups */
            $userGroups = $entity->getUserGroups()->toArray();
            $this->userGroups = $userGroups;
        }

        return $this;
    }

    /**
     * Method to update User entity password.
     *
     * @param Entity $entity
     * @param string $value
     *
     * @return User
     */
    protected function updatePassword(Entity $entity, string $value): self
    {
        $entity->setPlainPassword($value);

        return $this;
    }

    /**
     * Method to update User entity user groups.
     *
     * @param UserGroupAwareInterface $entity
     * @param array|UserGroupEntity[] $value
     */
    protected function updateUserGroups(UserGroupAwareInterface $entity, array $value): void
    {
        $entity->clearUserGroups();
        array_map([$entity, 'addUserGroup'], $value);
    }
}
