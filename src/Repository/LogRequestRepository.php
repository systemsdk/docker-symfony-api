<?php
declare(strict_types = 1);
/**
 * /src/Repository/LogRequestRepository.php
 */

namespace App\Repository;

use App\Entity\LogRequest as Entity;
use Exception;

/**
 * Class LogRequestRepository
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
class LogRequestRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    /**
     * Helper method to clean history data from log_request table.
     *
     * @return int
     *
     * @throws Exception
     */
    public function cleanHistory(): int
    {
        // Create query builder and define delete query
        $queryBuilder = $this
            ->createQueryBuilder('requestLog')
            ->delete()
            ->where("requestLog.date < DATESUB(NOW(), :days, 'DAY')")
            ->setParameter('days', (int)$_ENV['DATABASE_LOG_REQUEST_HISTORY_DAYS']);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
