<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/UserGroup/UserGroupCreate.php
 */

namespace App\DTO\UserGroup;

use App\Entity\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGroupCreate
 *
 * @package App\DTO\UserGroup
 */
class UserGroupCreate extends UserGroup
{
    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected ?Role $role = null;
}
