<?php

declare(strict_types=1);

namespace App\ApiKey\Application\DTO\ApiKey;

use App\ApiKey\Domain\Entity\ApiKey as Entity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Domain\Entity\Interfaces\UserGroupAwareInterface;
use App\User\Domain\Entity\UserGroup as UserGroupEntity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

use function array_map;

/**
 * @package App\ApiKey
 *
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class ApiKey extends RestDto
{
    /**
     * @var array<string, string>
     */
    protected static array $mappings = [
        'userGroups' => 'updateUserGroups',
    ];

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected string $description = '';

    protected string $token = '';

    /**
     * @var UserGroupEntity[]|array<int, UserGroupEntity>
     */
    #[AppAssert\EntityReferenceExists(UserGroupEntity::class)]
    protected array $userGroups = [];

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->setVisited('token');
        $this->token = $token;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

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
            $this->token = $entity->getToken();
            $this->description = $entity->getDescription();
            /** @var array<int, UserGroupEntity> $groups */
            $groups = $entity->getUserGroups()->toArray();
            $this->userGroups = $groups;
        }

        return $this;
    }

    /**
     * Method to update ApiKey entity user groups.
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
