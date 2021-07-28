<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogRequest as Entity;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class LogRequestRepository
 *
 * @package App\Repository
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null find(string $id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Entity|null findAdvanced(string $id, string | int | null $hydrationMode = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Entity[] findByAdvanced(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null)
 * @method Entity[] findAll()
 *
 * @codingStandardsIgnoreEnd
 */
class LogRequestRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
        private int $databaseLogRequestHistoryDays,
    ) {
    }

    /**
     * Helper method to clean history data from log_request table.
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
            ->setParameter('days', $this->databaseLogRequestHistoryDays);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
