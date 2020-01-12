<?php
declare(strict_types = 1);
/**
 * /src/DTO/ApiKey/ApiKeyPatch.php
 */

namespace App\DTO\ApiKey;

use App\DTO\Traits\PatchUserGroups;

/**
 * Class ApiKeyPatch
 *
 * @package App\DTO\ApiKey
 */
class ApiKeyPatch extends ApiKey
{
    use PatchUserGroups;
}
