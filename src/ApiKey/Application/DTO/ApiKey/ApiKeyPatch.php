<?php

declare(strict_types=1);

namespace App\ApiKey\Application\DTO\ApiKey;

use App\User\Application\DTO\Traits\PatchUserGroups;

/**
 * @package App\ApiKey
 */
class ApiKeyPatch extends ApiKey
{
    use PatchUserGroups;
}
