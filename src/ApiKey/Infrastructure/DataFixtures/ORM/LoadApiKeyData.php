<?php

declare(strict_types=1);

namespace App\ApiKey\Infrastructure\DataFixtures\ORM;

use App\ApiKey\Domain\Entity\ApiKey;
use App\General\Domain\Rest\UuidHelper;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;
use Throwable;

use function array_map;
use function str_pad;

/**
 * @package App\ApiKey
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LoadApiKeyData extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var array<string, non-empty-string>
     */
    public static array $uuids = [
        '' => '30000000-0000-1000-8000-000000000001',
        '-logged' => '30000000-0000-1000-8000-000000000002',
        '-api' => '30000000-0000-1000-8000-000000000003',
        '-user' => '30000000-0000-1000-8000-000000000004',
        '-admin' => '30000000-0000-1000-8000-000000000005',
        '-root' => '30000000-0000-1000-8000-000000000006',
    ];

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
        array_map(
            fn (?string $role): bool => $this->createApiKey($manager, $role),
            [
                null,
                ...$this->rolesService->getRoles(),
            ],
        );

        // Flush database changes
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     */
    #[Override]
    public function getOrder(): int
    {
        return 4;
    }

    public static function getUuidByKey(string $key): string
    {
        return self::$uuids[$key];
    }

    /**
     * Helper method to create new ApiKey entity with specified role.
     *
     * @throws Throwable
     */
    private function createApiKey(ObjectManager $manager, ?string $role = null): true
    {
        // Create new entity
        $entity = new ApiKey()
            ->setDescription('ApiKey Description: ' . ($role === null ? '' : $this->rolesService->getShort($role)))
            ->setToken(str_pad($role === null ? '' : $this->rolesService->getShort($role), ApiKey::TOKEN_LENGTH, '_'));
        $suffix = '';

        if ($role !== null) {
            /** @var UserGroup $userGroup */
            $userGroup = $this->getReference('UserGroup-' . $this->rolesService->getShort($role), UserGroup::class);
            $entity->addUserGroup($userGroup);
            $suffix = '-' . $this->rolesService->getShort($role);
        }

        PhpUnitUtil::setProperty(
            'id',
            UuidHelper::fromString(self::$uuids[$suffix]),
            $entity
        );

        // Persist entity
        $manager->persist($entity);
        // Create reference for later usage
        $this->addReference('ApiKey' . $suffix, $entity);

        return true;
    }
}
