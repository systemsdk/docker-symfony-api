<?php

declare(strict_types=1);

namespace App\User\Transport\AutoMapper\UserGroup;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Role\Application\Resource\RoleResource;
use App\Role\Domain\Entity\Role;
use Throwable;

/**
 * Class RequestMapper
 *
 * @package App\User
 */
class RequestMapper extends RestRequestMapper
{
    /**
     * @var array<int, string>
     */
    protected static array $properties = [
        'name',
        'role',
    ];

    public function __construct(
        private RoleResource $roleResource,
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function transformRole(string $role): Role
    {
        return $this->roleResource->getReference($role);
    }
}
