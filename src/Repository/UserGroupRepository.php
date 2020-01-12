<?php
declare(strict_types = 1);
/**
 * /src/Repository/UserGroupRepository.php
 */

namespace App\Repository;

use App\Entity\UserGroup as Entity;

/**
 * Class UserGroupRepository
 *
 * @package App\Repository
 *
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null   find(string $id, ?int $lockMode = null, ?int $lockVersion = null): ?Entity
 * @method array|Entity  findAdvanced(string $id, $hydrationMode = null)
 * @method Entity|null   findOneBy(array $criteria, ?array $orderBy = null): ?Entity
 * @method array         findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
 * @method array         findByAdvanced(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null): array
 * @method array         findAll(): array
 *
 * @codingStandardsIgnoreEnd
 */
class UserGroupRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;
    protected static array $searchColumns = ['role', 'name'];
}
