<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogLogin as Entity;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class LogLoginRepository
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
class LogLoginRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
        private int $databaseLogLoginHistoryDays,
    ) {
    }

    /**
     * Method to clean history data from 'log_login' table.
     *
     * @throws Exception
     */
    public function cleanHistory(): int
    {
        // Create query builder
        $queryBuilder = $this
            ->createQueryBuilder('ll')
            ->delete()
            ->where("ll.date < DATESUB(NOW(), :days, 'DAY')")
            ->setParameter('days', $this->databaseLogLoginHistoryDays);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
