<?php

declare(strict_types=1);

namespace App\User\Application\DTO\Traits;

use App\User\Domain\Entity\Interfaces\UserGroupAwareInterface;
use App\User\Domain\Entity\UserGroup as UserGroupEntity;

use function array_map;

/**
 * @package App\User
 */
trait PatchUserGroups
{
    /**
     * Method to patch entity user groups.
     *
     * @param array<int, UserGroupEntity> $value
     */
    protected function updateUserGroups(UserGroupAwareInterface $entity, array $value): self
    {
        array_map(
            static fn (UserGroupEntity $userGroup): UserGroupAwareInterface => $entity->addUserGroup($userGroup),
            $value,
        );

        return $this;
    }
}
