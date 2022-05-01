<?php

declare(strict_types=1);

namespace App\Role\Application\Security;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Domain\Entity\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use function array_key_exists;
use function array_unique;
use function array_values;
use function mb_strpos;
use function mb_strtolower;
use function mb_substr;

/**
 * Class RolesService
 *
 * @package App\Role
 */
class RolesService implements RolesServiceInterface
{
    /**
     * @var array<string, string>
     */
    private static array $roleNames = [
        Role::ROLE_LOGGED => 'Logged in users',
        Role::ROLE_USER => 'Normal users',
        Role::ROLE_ADMIN => 'Admin users',
        Role::ROLE_ROOT => 'Root users',
        Role::ROLE_API => 'API users',
    ];

    public function __construct(
        private RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    public function getRoles(): array
    {
        return [
            Role::ROLE_LOGGED,
            Role::ROLE_USER,
            Role::ROLE_ADMIN,
            Role::ROLE_ROOT,
            Role::ROLE_API,
        ];
    }

    public function getRoleLabel(string $role): string
    {
        $output = 'Unknown - ' . $role;

        if (array_key_exists($role, self::$roleNames)) {
            $output = self::$roleNames[$role];
        }

        return $output;
    }

    public function getShort(string $role): string
    {
        $offset = mb_strpos($role, '_');
        $offset = $offset !== false ? $offset + 1 : 0;

        return mb_strtolower(mb_substr($role, $offset));
    }

    public function getInheritedRoles(array $roles): array
    {
        return array_values(array_unique($this->roleHierarchy->getReachableRoleNames($roles)));
    }
}
