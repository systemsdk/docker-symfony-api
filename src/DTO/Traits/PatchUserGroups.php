<?php
declare(strict_types = 1);
/**
 * /src/DTO/Traits/PatchUserGroups.php
 */

namespace App\DTO\Traits;

use App\Entity\Interfaces\UserGroupAwareInterface;
use App\Entity\UserGroup as UserGroupEntity;

/**
 * Trait PatchUserGroups
 *
 * @package App\DTO\Traits
 */
trait PatchUserGroups
{
    /**
     * Method to patch entity user groups.
     *
     * @param array<int, UserGroupEntity> $value
     */
    protected function updateUserGroups(UserGroupAwareInterface $entity, array $value): void
    {
        array_map([$entity, 'addUserGroup'], $value);
    }
}
