<?php
declare(strict_types = 1);
/**
 * /src/Security/Interfaces/RolesInterface.php
 */

namespace App\Security\Interfaces;

/**
 * Interface RolesInterface
 *
 * @package Security
 */
interface RolesServiceInterface
{
    // Used role constants
    public const ROLE_LOGGED = 'ROLE_LOGGED';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ROOT = 'ROLE_ROOT';
    public const ROLE_API = 'ROLE_API';

    /**
     * Constructor
     *
     * @param array $rolesHierarchy
     */
    public function __construct(array $rolesHierarchy);

    /**
     * Getter for role hierarchy.
     *
     * @return array
     */
    public function getHierarchy(): array;

    /**
     * Getter method to return all roles in single dimensional array.
     *
     * @return array
     */
    public function getRoles(): array;

    /**
     * Getter method for role label.
     *
     * @param string $role
     *
     * @return string
     */
    public function getRoleLabel(string $role): string;

    /**
     * Getter method for short role.
     *
     * @param string $role
     *
     * @return string
     */
    public function getShort(string $role): string;

    /**
     * @param array $roles
     *
     * @return array
     */
    public function getInheritedRoles(array $roles): array;
}
