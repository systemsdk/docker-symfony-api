<?php
declare(strict_types = 1);
/**
 * /src/Repository/HealthRepository.php
 */

namespace App\Repository;

use App\Entity\Health as Entity;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Throwable;

/**
 * Class HealthRepository
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
class HealthRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    /**
     * Method to read value from database
     *
     * @throws NonUniqueResultException
     *
     * @return Entity|null
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
     *
     * @return Entity
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
     *
     * @return int
     */
    public function cleanup(): int
    {
        // Determine date
        $date = new DateTime('NOW', new DateTimeZone('UTC'));
        $date->sub(new DateInterval('P' . $_ENV['DATABASE_HEALTH_HISTORY_DAYS'] . 'D'));
        // Create query builder
        $queryBuilder = $this
            ->createQueryBuilder('h')
            ->delete()
            ->where('h.timestamp < :timestamp')
            ->setParameter('timestamp', $date);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
