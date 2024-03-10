<?php

declare(strict_types=1);

namespace App\User\Application\DTO\UserGroup;

use App\General\Application\Validator\Constraints as AppAssert;
use App\Role\Domain\Entity\Role as RoleEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User
 */
class UserGroupCreate extends UserGroup
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(entityClass: RoleEntity::class)]
    protected ?RoleEntity $role = null;
}
