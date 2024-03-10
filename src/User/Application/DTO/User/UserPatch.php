<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Application\DTO\Traits\PatchUserGroups;

/**
 * @package App\User
 */
class UserPatch extends User
{
    use PatchUserGroups;
}
