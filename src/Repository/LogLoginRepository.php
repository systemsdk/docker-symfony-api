<?php
declare(strict_types = 1);
/**
 * /src/Repository/LogLoginRepository.php
 */

namespace App\Repository;

use App\Entity\LogLogin as Entity;
use Exception;

/**
 * Class LogLoginRepository
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
class LogLoginRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    /**
     * Method to clean history data from 'log_login' table.
     *
     * @throws Exception
     *
     * @return int
     */
    public function cleanHistory(): int
    {
        // Create query builder
        $queryBuilder = $this
            ->createQueryBuilder('ll')
            ->delete()
            ->where("ll.date < DATESUB(NOW(), :days, 'DAY')")
            ->setParameter('days', (int)$_ENV['DATABASE_LOG_LOGIN_HISTORY_DAYS']);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
