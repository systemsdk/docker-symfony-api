<?php
declare(strict_types = 1);
/**
 * /src/Rest/Interfaces/RepositoryInterface.php
 */

namespace App\Rest\Interfaces;

use App\Entity\Interfaces\EntityInterface;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

/**
 * Interface RepositoryInterface
 *
 * TODO: Deprecated, see App\Repository\Interfaces\BaseRepositoryInterface
 *
 * @package App\Rest\Interfaces
 */
interface RepositoryInterface
{
    /**
     * Getter method for entity name.
     *
     * @return string
     */
    public function getEntityName(): string;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @param string $id
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return Proxy|null
     *
     * @psalm-suppress DeprecatedClass
     */
    public function getReference(string $id): ?Proxy;

    /**
     * Gets all association mappings of the class.
     *
     * @return array
     */
    public function getAssociations(): array;

    /**
     * Getter method for search columns of current entity.
     *
     * @return array
     */
    public function getSearchColumns(): array;

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * Helper method to persist specified entity to database.
     *
     * @param EntityInterface $entity
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return RepositoryInterface
     */
    public function save(EntityInterface $entity): self;

    /**
     * Helper method to remove specified entity from database.
     *
     * @param EntityInterface $entity
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return RepositoryInterface
     */
    public function remove(EntityInterface $entity): self;

    /**
     * Generic count method to determine count of entities for specified criteria and search term(s).
     *
     * @param array|null $criteria
     * @param array|null $search
     *
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int
     */
    public function countAdvanced(?array $criteria = null, ?array $search = null): int;

    /**
     * Generic replacement for basic 'findBy' method if/when you want to use generic LIKE search.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @param array|null $search
     *
     * @return EntityInterface[]
     */
    public function findByAdvanced(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null
    ): array;

    /**
     * Repository method to fetch current entity id values from database and return those as an array.
     *
     * @param array|null $criteria
     * @param array|null $search
     *
     * @return array
     */
    public function findIds(?array $criteria = null, ?array $search = null): array;

    /**
     * Helper method to 'reset' repository entity table - in other words delete all records - so be carefully with
     * this...
     *
     * @return int
     */
    public function reset(): int;

    /**
     * With this method you can attach some custom functions for generic REST API find / count queries.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function processQueryBuilder(QueryBuilder $queryBuilder): void;

    /**
     * Adds left join to current QueryBuilder query.
     *
     * @note Requires processJoins() to be run
     *
     * @see QueryBuilder::leftJoin() for parameters
     *
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     *
     * @return RepositoryInterface
     */
    public function addLeftJoin(array $parameters): self;

    /**
     * Adds inner join to current QueryBuilder query.
     *
     * @note Requires processJoins() to be run
     *
     * @see QueryBuilder::innerJoin() for parameters
     *
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     *
     * @return RepositoryInterface
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
     * @param callable   $callable
     * @param array|null $args
     *
     * @return RepositoryInterface
     */
    public function addCallback(callable $callable, ?array $args = null): self;

    /**
     * Process defined joins for current QueryBuilder instance.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function processJoins(QueryBuilder $queryBuilder): void;

    /**
     * Process defined callbacks for current QueryBuilder instance.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function processCallbacks(QueryBuilder $queryBuilder): void;
}
