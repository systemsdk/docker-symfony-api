<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Repository\Traits;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Rest\UuidHelper;
use App\General\Infrastructure\Rest\RepositoryHelper;
use ArrayIterator;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;
use InvalidArgumentException;

use function array_column;
use function assert;

/**
 * @package App\General
 */
trait RepositoryMethodsTrait
{
    /**
     * Wrapper for default Doctrine repository find method.
     *
     * @throws TransactionRequiredException
     * @throws OptimisticLockException
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function find(
        string $id,
        LockMode|int|null $lockMode = null,
        ?int $lockVersion = null,
        ?string $entityManagerName = null
    ): ?EntityInterface {
        $output = $this->getEntityManager($entityManagerName)->find(
            $this->getEntityName(),
            $id,
            $lockMode,
            $lockVersion
        );

        return $output instanceof EntityInterface ? $output : null;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-param string|AbstractQuery::HYDRATE_*|null $hydrationMode
     */
    public function findAdvanced(
        string $id,
        string|int|null $hydrationMode = null,
        string|null $entityManagerName = null
    ): null|array|EntityInterface {
        // Get query builder
        $queryBuilder = $this->getQueryBuilder(entityManagerName: $entityManagerName);
        // Process custom QueryBuilder actions
        $this->processQueryBuilder($queryBuilder);
        $queryBuilder
            ->where('entity.id = :id')
            ->setParameter('id', $id, UuidHelper::getType($id));
        /*
         * This is just to help debug queries
         *
         * dd($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getSQL());
         */

        return $queryBuilder->getQuery()->getOneOrNullResult($hydrationMode);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null): ?object
    {
        $repository = $this->getEntityManager($entityManagerName)->getRepository($this->getEntityName());

        return $repository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return list<object|EntityInterface>
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $entityManagerName = null
    ): array {
        return $this
            ->getEntityManager($entityManagerName)
            ->getRepository($this->getEntityName())
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, EntityInterface>
     */
    public function findByAdvanced(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null
    ): array {
        // Get query builder
        $queryBuilder = $this->getQueryBuilder($criteria, $search, $orderBy, $limit, $offset, $entityManagerName);
        // Process custom QueryBuilder actions
        $this->processQueryBuilder($queryBuilder);
        /*
         * This is just to help debug queries
         *
         * dd($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getSQL());
         */
        RepositoryHelper::resetParameterCount();

        $iterator = new Paginator($queryBuilder, true)->getIterator();

        assert($iterator instanceof ArrayIterator);

        return $iterator->getArrayCopy();
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return list<object|EntityInterface>
     */
    public function findAll(?string $entityManagerName = null): array
    {
        return $this
            ->getEntityManager($entityManagerName)
            ->getRepository($this->getEntityName())
            ->findAll();
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, string>
     */
    public function findIds(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): array
    {
        // Get query builder
        $queryBuilder = $this->getQueryBuilder(
            criteria: $criteria,
            search: $search,
            entityManagerName: $entityManagerName
        );
        // Build query
        $queryBuilder
            ->select('entity.id')
            ->distinct();
        // Process custom QueryBuilder actions
        $this->processQueryBuilder($queryBuilder);
        /*
         * This is just to help debug queries
         *
         * dd($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getSQL());
         */
        RepositoryHelper::resetParameterCount();

        return array_column($queryBuilder->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Generic count method to determine count of entities for specified criteria and search term(s).
     *
     * @throws InvalidArgumentException|NonUniqueResultException|NoResultException
     */
    public function countAdvanced(
        ?array $criteria = null,
        ?array $search = null,
        ?string $entityManagerName = null
    ): int {
        // Get query builder
        $queryBuilder = $this->getQueryBuilder(
            criteria: $criteria,
            search: $search,
            entityManagerName: $entityManagerName
        );
        // Build query
        $queryBuilder->select('COUNT(DISTINCT(entity.id))');
        // Process custom QueryBuilder actions
        $this->processQueryBuilder($queryBuilder);
        /*
         * This is just to help debug queries
         *
         * dd($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getSQL());
         */
        RepositoryHelper::resetParameterCount();

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Helper method to 'reset' repository entity table - in other words delete all records
     */
    public function reset(?string $entityManagerName = null): int
    {
        // Create query builder
        $queryBuilder = $this->createQueryBuilder(entityManagerName: $entityManagerName);
        // Define delete query
        $queryBuilder->delete();

        // Return deleted row count
        return (int)$queryBuilder->getQuery()->execute();
    }

    /**
     * Helper method to get QueryBuilder for current instance within specified default parameters.
     *
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, string>|null $search
     * @param array<string, string>|null $orderBy
     *
     * @throws InvalidArgumentException
     */
    private function getQueryBuilder(
        ?array $criteria = null,
        ?array $search = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $entityManagerName = null
    ): QueryBuilder {
        // Create new QueryBuilder for this instance
        $queryBuilder = $this->createQueryBuilder(entityManagerName: $entityManagerName);
        // Process normal and search term criteria
        RepositoryHelper::processCriteria($queryBuilder, $criteria);
        RepositoryHelper::processSearchTerms($queryBuilder, $this->getSearchColumns(), $search);
        RepositoryHelper::processOrderBy($queryBuilder, $orderBy);
        // Process limit and offset
        $queryBuilder->setMaxResults($limit);
        $queryBuilder->setFirstResult($offset ?? 0);

        return $queryBuilder;
    }
}
