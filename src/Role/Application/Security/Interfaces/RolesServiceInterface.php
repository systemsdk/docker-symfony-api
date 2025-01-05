<?php

declare(strict_types=1);

namespace App\Role\Application\Security\Interfaces;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * @package App\Role
 */
interface RolesServiceInterface
{
    public function __construct(RoleHierarchyInterface $roleHierarchy);

    /**
     * Getter method to return all roles in single dimensional array.
     *
     * @return array<int, non-empty-string>
     */
    public function getRoles(): array;

    /**
     * Getter method for role label.
     */
    public function getRoleLabel(string $role): string;

    /**
     * Getter method for short role.
     */
    public function getShort(string $role): string;

    /**
     * Helper method to get inherited roles for given roles.
     *
     * @param array<int, string> $roles
     *
     * @return array<int, string>
     */
    public function getInheritedRoles(array $roles): array;
}
