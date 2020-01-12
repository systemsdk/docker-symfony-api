<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/User/UserPatch.php
 */

namespace App\DTO\User;

use App\DTO\Traits\PatchUserGroups;

/**
 * Class UserPatch
 *
 * @package App\DTO\User
 */
class UserPatch extends User
{
    use PatchUserGroups;
}
