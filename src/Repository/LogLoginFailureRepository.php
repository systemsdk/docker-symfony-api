<?php
declare(strict_types = 1);
/**
 * /src/Repository/LogLoginFailureRepository.php
 */

namespace App\Repository;

use App\Entity\LogLoginFailure as Entity;
use App\Entity\User;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;

/**
 * Class LogLoginFailureRepository
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
class LogLoginFailureRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;

    /**
     * Method to clear specified user login failures.
     *
     * @param User $user
     *
     * @return int
     */
    public function clear(User $user): int
    {
        // Create query builder and define delete query
        $queryBuilder = $this
            ->createQueryBuilder('logLoginFailure')
            ->delete()
            ->where('logLoginFailure.user = :user')
            ->setParameter('user', $user->getId(), UuidBinaryOrderedTimeType::NAME);

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }
}
