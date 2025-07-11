<?php

declare(strict_types=1);

namespace App\General\Domain\Repository\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\TransactionRequiredException;
use InvalidArgumentException;
use Throwable;

/**
 * @package App\General
 */
interface BaseRepositoryInterface
{
    /**
     * Getter method for entity name.
     */
    public function getEntityName(): string;

    /**
     * Getter method for search columns of current entity.
     *
     * @return array<int, string>
     */
    public function getSearchColumns(): array;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @throws ORMException
     */
    public function getReference(string $id, ?string $entityManagerName = null): ?object;

    /**
     * Gets all association mappings of the class.
     *
     * @psalm-return array<string, AssociationMapping>
     */
    public function getAssociations(?string $entityManagerName = null): array;

    /**
     * Returns the ORM metadata descriptor for a class.
     */
    public function getClassMetaData(?string $entityManagerName = null): ClassMetadata;

    /**
     * Getter method for EntityManager for current entity.
     */
    public function getEntityManager(?string $entityManagerName = null): EntityManager;

    /**
     * Method to create new query builder for current entity.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     */
    public function createQueryBuilder(
        ?string $alias = null,
        ?string $indexBy = null,
        ?string $entityManagerName = null
    ): QueryBuilder;

    /**
     * Wrapper for default Doctrine repository find method.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function find(
        string $id,
        LockMode|int|null $lockMode = null,
        ?int $lockVersion = null,
        ?string $entityManagerName = null
    ): ?EntityInterface;

    /**
     * Advanced version of find method, with this you can process query as you like, eg. add joins and callbacks to
     * modify / optimize current query.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @psalm-param string|AbstractQuery::HYDRATE_*|null $hydrationMode
     *
     * @psalm-return array<int|string, mixed>|EntityInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findAdvanced(
        string $id,
        string|int|null $hydrationMode = null,
        string|null $entityManagerName = null
    ): null|array|EntityInterface;

    /**
     * Wrapper for default Doctrine repository findOneBy method.
     *
     * @psalm-param array<string, mixed> $criteria
     * @psalm-param array<string, string>|null $orderBy
     *
     * @psalm-return EntityInterface|object|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null): ?object;

    /**
     * Wrapper for default Doctrine repository findBy method.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @psalm-param array<string, mixed> $criteria
     * @psalm-param array<string, string>|null $orderBy
     * @phpstan-param array<string, 'asc'|'desc'|'ASC'|'DESC'>|null $orderBy
     *
     * @psalm-return list<object|EntityInterface>
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $entityManagerName = null
    ): array;

    /**
     * Generic replacement for basic 'findBy' method if/when you want to use generic LIKE search.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @param array<int|string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @param array<string, string>|null $search
     *
     * @throws Throwable
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
    ): array;

    /**
     * Wrapper for default Doctrine repository findAll method.
     *
     * @psalm-return list<object|EntityInterface>
     */
    public function findAll(?string $entityManagerName = null): array;

    /**
     * Repository method to fetch current entity id values from database and return those as an array.
     *
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, string>|null $search
     *
     * @return array<int, string>
     *
     * @throws InvalidArgumentException
     */
    public function findIds(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): array;

    /**
     * Generic count method to determine count of entities for specified criteria and search term(s).
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, string>|null $search
     *
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAdvanced(
        ?array $criteria = null,
        ?array $search = null,
        ?string $entityManagerName = null
    ): int;

    /**
     * Helper method to 'reset' repository entity table - in other words delete all records - so be carefully with
     * this...
     */
    public function reset(?string $entityManagerName = null): int;

    /**
     * Helper method to persist specified entity to database.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self;

    /**
     * Helper method to remove specified entity from database.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self;

    /**
     * With this method you can attach some custom functions for generic REST API find / count queries.
     */
    public function processQueryBuilder(QueryBuilder $queryBuilder): void;

    /**
     * Adds left join to current QueryBuilder query.
     *
     * @note Requires processJoins() to be run
     *
     * @see QueryBuilder::leftJoin() for parameters
     *
     * @param array<int, scalar> $parameters
     *
     * @throws InvalidArgumentException
     */
    public function addLeftJoin(array $parameters): self;

    /**
     * Adds inner join to current QueryBuilder query.
     *
     * @note Requires processJoins() to be run
     *
     * @see QueryBuilder::innerJoin() for parameters
     *
     * @param array<int, scalar> $parameters
     *
     * @throws InvalidArgumentException
     */
    public function addInnerJoin(array $parameters): self;

    /**
     * Method to add callback to current query builder instance which is calling 'processQueryBuilder' method. By
     * default this method is called from following core methods:
     *  - countAdvanced
     *  - findByAdvanced
     *  - findIds
     *
     * Note that every callback will get 'QueryBuilder' as in first parameter.
     *
     * @param array<int, mixed>|null $args
     */
    public function addCallback(callable $callable, ?array $args = null): self;
}
