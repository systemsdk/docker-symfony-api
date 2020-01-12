<?php
declare(strict_types = 1);
/**
 * /src/Security/Roles.php
 */

namespace App\Security;

use App\Security\Interfaces\RolesServiceInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

/**
 * Class Roles
 *
 * @package App\Security
 */
class RolesService implements RolesServiceInterface
{
    /**
     * Roles hierarchy.
     *
     * @var array<string, array<int, string>>
     */
    private array $rolesHierarchy;

    /**
     * @var array<string, string>
     */
    private static array $roleNames = [
        self::ROLE_LOGGED => 'Logged in users',
        self::ROLE_USER => 'Normal users',
        self::ROLE_ADMIN => 'Admin users',
        self::ROLE_ROOT => 'Root users',
        self::ROLE_API => 'API users',
    ];

    /**
     * Constructor
     *
     * @param array $rolesHierarchy
     */
    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    /**
     * Getter for role hierarchy.
     *
     * @return array
     */
    public function getHierarchy(): array
    {
        return $this->rolesHierarchy;
    }

    /**
     * Getter method to return all roles in single dimensional array.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return [
            self::ROLE_LOGGED,
            self::ROLE_USER,
            self::ROLE_ADMIN,
            self::ROLE_ROOT,
            self::ROLE_API,
        ];
    }

    /**
     * Getter method for role label.
     *
     * @param string $role
     *
     * @return string
     */
    public function getRoleLabel(string $role): string
    {
        $output = 'Unknown - ' . $role;

        if (array_key_exists($role, self::$roleNames)) {
            $output = self::$roleNames[$role];
        }

        return $output;
    }

    /**
     * Getter method for short role.
     *
     * @param string $role
     *
     * @return string
     */
    public function getShort(string $role): string
    {
        $offset = mb_strpos($role, '_');
        $offset = $offset !== false ? $offset + 1 : 0;

        return mb_strtolower(mb_substr($role, $offset));
    }

    /**
     * Helper method to get inherited roles for given roles.
     *
     * @param array $roles
     *
     * @return array
     */
    public function getInheritedRoles(array $roles): array
    {
        return array_values(array_unique((new RoleHierarchy($this->rolesHierarchy))->getReachableRoleNames($roles)));
    }
}
