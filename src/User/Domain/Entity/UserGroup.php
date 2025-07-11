<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\ApiKey\Domain\Entity\ApiKey;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Role\Domain\Entity\Role;
use App\User\Domain\Entity\Traits\Blameable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Throwable;

/**
 * @package App\User
 */
#[ORM\Entity]
#[ORM\Table(name: 'user_group')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class UserGroup implements EntityInterface, Stringable
{
    use Blameable;
    use Timestampable;
    use Uuid;

    final public const string SET_USER_PROFILE_GROUPS = 'set.UserProfileGroups';
    final public const string SET_USER_GROUP_BASIC = 'set.UserGroupBasic';

    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: UuidBinaryOrderedTimeType::NAME,
        unique: true,
    )]
    #[Groups([
        'UserGroup',
        'UserGroup.id',

        'ApiKey.userGroups',
        'User.userGroups',
        'Role.userGroups',

        User::SET_USER_PROFILE,
        self::SET_USER_PROFILE_GROUPS,
        self::SET_USER_GROUP_BASIC,
    ])]
    private UuidInterface $id;

    #[ORM\ManyToOne(
        targetEntity: Role::class,
        inversedBy: 'userGroups',
    )]
    #[ORM\JoinColumn(
        name: 'role',
        referencedColumnName: 'role',
        onDelete: 'CASCADE',
    )]
    #[Groups([
        'UserGroup.role',

        User::SET_USER_PROFILE,
        self::SET_USER_PROFILE_GROUPS,
        self::SET_USER_GROUP_BASIC,
    ])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Role $role;

    #[ORM\Column(
        name: 'name',
        type: Types::STRING,
        length: 255,
    )]
    #[Groups([
        'UserGroup',
        'UserGroup.name',

        User::SET_USER_PROFILE,
        self::SET_USER_PROFILE_GROUPS,
        self::SET_USER_GROUP_BASIC,
    ])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 2,
        max: 255,
    )]
    private string $name = '';

    /**
     * @var Collection<int, User>|ArrayCollection<int, User>
     */
    #[ORM\ManyToMany(
        targetEntity: User::class,
        mappedBy: 'userGroups',
    )]
    #[Groups([
        'UserGroup.users',
    ])]
    private Collection | ArrayCollection $users;

    /**
     * @var Collection<int, ApiKey>|ArrayCollection<int, ApiKey>
     */
    #[ORM\ManyToMany(
        targetEntity: ApiKey::class,
        mappedBy: 'userGroups',
    )]
    #[Groups([
        'UserGroup.apiKeys',
    ])]
    private Collection | ArrayCollection $apiKeys;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->users = new ArrayCollection();
        $this->apiKeys = new ArrayCollection();
    }

    #[Override]
    public function __toString(): string
    {
        return self::class;
    }

    #[Override]
    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>|ArrayCollection<int, User>
     */
    public function getUsers(): Collection | ArrayCollection
    {
        return $this->users;
    }

    /**
     * @return Collection<int, ApiKey>|ArrayCollection<int, ApiKey>
     */
    public function getApiKeys(): Collection | ArrayCollection
    {
        return $this->apiKeys;
    }

    /**
     * Method to attach new user group to user.
     */
    public function addUser(User $user): self
    {
        if ($this->users->contains($user) === false) {
            $this->users->add($user);
            $user->addUserGroup($this);
        }

        return $this;
    }

    /**
     * Method to remove specified user from user group.
     */
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeUserGroup($this);
        }

        return $this;
    }

    /**
     * Method to remove all many-to-many user relations from current user group.
     */
    public function clearUsers(): self
    {
        $this->users->clear();

        return $this;
    }

    /**
     * Method to attach new user group to user.
     */
    public function addApiKey(ApiKey $apiKey): self
    {
        if ($this->apiKeys->contains($apiKey) === false) {
            $this->apiKeys->add($apiKey);
            $apiKey->addUserGroup($this);
        }

        return $this;
    }

    /**
     * Method to remove specified user from user group.
     */
    public function removeApiKey(ApiKey $apiKey): self
    {
        if ($this->apiKeys->removeElement($apiKey)) {
            $apiKey->removeUserGroup($this);
        }

        return $this;
    }

    /**
     * Method to remove all many-to-many api key relations from current user group.
     */
    public function clearApiKeys(): self
    {
        $this->apiKeys->clear();

        return $this;
    }
}
