<?php

declare(strict_types=1);

namespace App\Log\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Log\Domain\Entity\LogRequest as Entity;
use App\Log\Domain\Repository\Interfaces\LogRequestRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @package App\Log
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findAdvanced(string $id, string|int|null $hydrationMode = null, string|null $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @method Entity[] findByAdvanced(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @method Entity[] findAll(?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 */
class LogRequestRepository extends BaseRepository implements LogRequestRepositoryInterface
{
    /**
     * @psalm-var class-string
     */
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
        private readonly int $databaseLogRequestHistoryDays,
    ) {
    }

    /**
     * {@inheritdoc}
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
