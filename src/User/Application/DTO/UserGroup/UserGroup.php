<?php

declare(strict_types=1);

namespace App\User\Application\DTO\UserGroup;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Role\Domain\Entity\Role as RoleEntity;
use App\User\Domain\Entity\UserGroup as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User
 *
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class UserGroup extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 4, max: 255)]
    protected string $name = '';

    #[AppAssert\EntityReferenceExists(entityClass: RoleEntity::class)]
    protected ?RoleEntity $role = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->setVisited('name');
        $this->name = $name;

        return $this;
    }

    public function getRole(): ?RoleEntity
    {
        return $this->role;
    }

    public function setRole(RoleEntity $role): self
    {
        $this->setVisited('role');
        $this->role = $role;

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
            $this->name = $entity->getName();
            $this->role = $entity->getRole();
        }

        return $this;
    }
}
