<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/UserGroup/UserGroupUpdate.php
 */

namespace App\DTO\UserGroup;

use App\Entity\Role;

/**
 * Class UserGroupUpdate
 *
 * @package App\DTO\UserGroup
 */
class UserGroupUpdate extends UserGroup
{
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    /**
     * @var \App\Entity\Role
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected ?Role $role;
}
