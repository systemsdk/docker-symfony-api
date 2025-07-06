<?php

declare(strict_types=1);

namespace App\Log\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Log\Domain\Entity\LogLoginFailure as Entity;
use App\Log\Domain\Repository\Interfaces\LogLoginFailureRepositoryInterface;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;

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
class LogLoginFailureRepository extends BaseRepository implements LogLoginFailureRepositoryInterface
{
    /**
     * @psalm-var class-string
     */
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    /**
     * {@inheritdoc}
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
