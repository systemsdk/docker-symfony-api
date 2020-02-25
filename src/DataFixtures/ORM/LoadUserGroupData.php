<?php
declare(strict_types = 1);
/**
 * /src/DataFixtures/ORM/LoadUserGroupData.php
 */

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Persistence\ObjectManager;
use App\Security\Interfaces\RolesServiceInterface;
use App\Entity\Role;
use App\Entity\UserGroup;
use Throwable;

/**
 * Class LoadUserGroupData
 *
 * @package App\DataFixtures\ORM
 */
final class LoadUserGroupData extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ContainerInterface $container;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ObjectManager $manager;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private RolesServiceInterface $roles;


    /**
     * Setter for container.
     *
     * @param ContainerInterface|null $container
     */
    public function setContainer(?ContainerInterface $container = null): void
    {
        if ($container !== null) {
            $this->container = $container;
        }
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws Throwable
     */
    public function load(ObjectManager $manager): void
    {
        /** @var RolesServiceInterface $rolesService */
        $rolesService = $this->container->get('test.app.security.roles_service');
        $this->roles = $rolesService;
        $this->manager = $manager;
        // Create entities
        array_map(fn (string $role): bool => $this->createUserGroup($role), $this->roles->getRoles());
        // Flush database changes
        $this->manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder(): int
    {
        return 2;
    }

    /**
     * Method to create UserGroup entity for specified role.
     *
     * @param string $role
     *
     * @throws Throwable
     *
     * @return bool
     */
    private function createUserGroup(string $role): bool
    {
        /** @var Role $roleReference */
        $roleReference = $this->getReference('Role-' . $this->roles->getShort($role));

        // Create new entity
        $entity = new UserGroup();
        $entity->setRole($roleReference);
        $entity->setName($this->roles->getRoleLabel($role));

        // Persist entity
        $this->manager->persist($entity);

        // Create reference for later usage
        $this->addReference('UserGroup-' . $this->roles->getShort($role), $entity);

        return true;
    }
}
