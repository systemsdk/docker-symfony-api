<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/UserGroup/UserGroupUpdate.php
 */

namespace App\DTO\UserGroup;

use App\Entity\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGroupUpdate
 *
 * @package App\DTO\UserGroup
 */
class UserGroupUpdate extends UserGroup
{
    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected ?Role $role = null;
}
