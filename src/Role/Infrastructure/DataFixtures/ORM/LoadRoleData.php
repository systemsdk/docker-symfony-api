<?php

declare(strict_types=1);

namespace App\Role\Infrastructure\DataFixtures\ORM;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Domain\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;
use Throwable;

use function array_map;

/**
 * @package App\Role
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LoadRoleData extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @throws Throwable
     */
    #[Override]
    public function load(ObjectManager $manager): void
    {
        // Create entities
        array_map(fn (string $role): bool => $this->createRole($manager, $role), $this->rolesService->getRoles());
        // Flush database changes
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     */
    #[Override]
    public function getOrder(): int
    {
        return 1;
    }

    /**
     * Method to create and persist role entity to database.
     *
     * @throws Throwable
     *
     * @param non-empty-string $role
     */
    private function createRole(ObjectManager $manager, string $role): true
    {
        // Create new Role entity
        $entity = new Role($role)
            ->setDescription('Description - ' . $role);

        // Persist entity
        $manager->persist($entity);

        // Create reference for later usage
        $this->addReference('Role-' . $this->rolesService->getShort($role), $entity);

        return true;
    }
}
