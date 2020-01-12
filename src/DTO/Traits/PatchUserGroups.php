<?php
declare(strict_types = 1);
/**
 * /src/DTO/Traits/PatchUserGroups.php
 */

namespace App\DTO\Traits;

use App\Entity\UserGroup as UserGroupEntity;
use App\Entity\Interfaces\UserGroupAwareInterface;

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
     * @param UserGroupAwareInterface $entity
     * @param array|UserGroupEntity[] $value
     */
    protected function updateUserGroups(UserGroupAwareInterface $entity, array $value): void
    {
        array_map([$entity, 'addUserGroup'], $value);
    }
}
