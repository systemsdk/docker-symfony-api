<?php

declare(strict_types=1);

namespace App\Tool\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Tool\Domain\Entity\Health as Entity;
use App\Tool\Domain\Repository\Interfaces\HealthRepositoryInterface;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Throwable;

/**
 * @package App\Tool
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
class HealthRepository extends BaseRepository implements HealthRepositoryInterface
{
    /**
     * @psalm-var class-string
     */
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
        private readonly int $databaseHealthHistoryDays,
    ) {
    }

    /**
     * Method to read value from database
     *
     * @throws NonUniqueResultException
     */
    public function read(): ?Entity
    {
        $query = $this
            ->createQueryBuilder('h')
            ->select('h')
            ->orderBy('h.timestamp', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Method to write new value to database.
     *
     * @throws Throwable
     */
    public function create(): Entity
    {
        // Create new entity
        $entity = new Entity();
        // Store entity to database
        $this->save($entity);

        return $entity;
    }

    /**
     * Method to cleanup 'health' table.
     *
     * @throws Exception
     */
    public function cleanup(): int
    {
        // Determine date
        $date = new DateTimeImmutable('NOW', new DateTimeZone('UTC'))
            ->sub(new DateInterval('P' . $this->databaseHealthHistoryDays . 'D'));
        // Create query builder
        $queryBuilder = $this
            ->createQueryBuilder('h')
            ->delete()
            ->where('h.timestamp < :timestamp')
            ->setParameter('timestamp', $date, Types::DATETIME_IMMUTABLE);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
