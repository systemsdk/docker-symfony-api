<?php

declare(strict_types=1);

namespace App\Role\Application\Service\Role\Interfaces;

use Throwable;

/**
 * @package App\Role
 */
interface SyncRolesServiceInterface
{
    /**
     * @return array{created: int, removed: int}
     *
     * @throws Throwable
     */
    public function syncRoles(): array;
}
