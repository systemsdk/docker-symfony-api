<?php

declare(strict_types=1);

namespace App\Role\Application\Service\Role;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Application\Service\Role\Interfaces\SyncRolesServiceInterface;
use App\Role\Domain\Entity\Role;
use App\Role\Domain\Repository\Interfaces\RoleRepositoryInterface;
use Throwable;

use function array_map;
use function array_sum;

/**
 * @package App\Role
 */
class SyncRolesService implements SyncRolesServiceInterface
{
    /**
     * @param \App\Role\Infrastructure\Repository\RoleRepository $roleRepository
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function syncRoles(): array
    {
        $created = array_sum(
            array_map(
                fn (string $role): int => $this->createRole($role),
                $this->rolesService->getRoles(),
            ),
        );
        $removed = $this->roleRepository->clearRoles($this->rolesService->getRoles());
        $this->roleRepository->getEntityManager()->flush();

        return [
            'created' => $created,
            'removed' => $removed,
        ];
    }

    /**
     * Method to check if specified role exists on database and if not create and persist it to database.
     *
     * @throws Throwable
     *
     * @param non-empty-string $role
     */
    private function createRole(string $role): int
    {
        $output = 0;

        if ($this->roleRepository->find($role) === null) {
            $entity = new Role($role);
            $entity->setDescription($this->rolesService->getRoleLabel($role));
            $this->roleRepository->save($entity, false);
            $output = 1;
        }

        return $output;
    }
}
