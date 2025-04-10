<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Repository;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\General\Infrastructure\Repository\Traits\RepositoryMethodsTrait;
use App\General\Infrastructure\Repository\Traits\RepositoryWrappersTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

use function array_map;
use function array_unshift;
use function implode;
use function in_array;
use function serialize;
use function sha1;
use function spl_object_hash;

/**
 * @package App\General
 */
#[AutoconfigureTag('app.rest.repository')]
#[AutoconfigureTag('app.stopwatch')]
abstract class BaseRepository implements BaseRepositoryInterface
{
    use RepositoryMethodsTrait;
    use RepositoryWrappersTrait;

    private const string INNER_JOIN = 'innerJoin';
    private const string LEFT_JOIN = 'leftJoin';

    /**
     * @psalm-var class-string
     */
    protected static string $entityName;

    /**
     * @var array<int, string>
     */
    protected static array $searchColumns = [];
    protected static EntityManager $entityManager;
    protected ManagerRegistry $managerRegistry;

    /**
     * Joins that need to attach to queries, this is needed for to prevent duplicate joins on those.
     *
     * @var array<string, array<array<int, scalar>>>
     */
    private static array $joins = [
        self::INNER_JOIN => [],
        self::LEFT_JOIN => [],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private static array $processedJoins = [
        self::INNER_JOIN => [],
        self::LEFT_JOIN => [],
    ];

    /**
     * @var array<int, array{0: callable, 1: array<mixed>}>
     */
    private static array $callbacks = [];

    /**
     * @var array<int, string>
     */
    private static array $processedCallbacks = [];

    /**
     * {@inheritdoc}
     *
     * @psalm-return class-string
     */
    #[Override]
    public function getEntityName(): string
    {
        return static::$entityName;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getSearchColumns(): array
    {
        return static::$searchColumns;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function save(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self
    {
        $flush ??= true;
        // Persist on database
        $this->getEntityManager($entityManagerName)->persist($entity);

        if ($flush) {
            $this->getEntityManager($entityManagerName)->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function remove(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self
    {
        $flush ??= true;
        // Remove from database
        $this->getEntityManager($entityManagerName)->remove($entity);

        if ($flush) {
            $this->getEntityManager($entityManagerName)->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function processQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // Reset processed joins and callbacks
        self::$processedJoins = [
            self::INNER_JOIN => [],
            self::LEFT_JOIN => [],
        ];
        self::$processedCallbacks = [];
        $this->processJoins($queryBuilder);
        $this->processCallbacks($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function addLeftJoin(array $parameters): self
    {
        if ($parameters !== []) {
            $this->addJoinToQuery(self::LEFT_JOIN, $parameters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function addInnerJoin(array $parameters): self
    {
        if ($parameters !== []) {
            $this->addJoinToQuery(self::INNER_JOIN, $parameters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function addCallback(callable $callable, ?array $args = null): self
    {
        $args ??= [];
        $hash = sha1(serialize([spl_object_hash((object)$callable), ...$args]));

        if (!in_array($hash, self::$processedCallbacks, true)) {
            self::$callbacks[] = [$callable, $args];
            self::$processedCallbacks[] = $hash;
        }

        return $this;
    }

    /**
     * Process defined joins for current QueryBuilder instance.
     */
    protected function processJoins(QueryBuilder $queryBuilder): void
    {
        foreach (self::$joins as $joinType => $joins) {
            array_map(
                static fn (array $joinParameters): QueryBuilder => $queryBuilder->{$joinType}(...$joinParameters),
                $joins,
            );

            self::$joins[$joinType] = [];
        }
    }

    /**
     * Process defined callbacks for current QueryBuilder instance.
     */
    protected function processCallbacks(QueryBuilder $queryBuilder): void
    {
        foreach (self::$callbacks as [$callback, $args]) {
            array_unshift($args, $queryBuilder);
            $callback(...$args);
        }

        self::$callbacks = [];
    }

    /**
     * Method to add defined join(s) to current QueryBuilder query. This will keep track of attached join(s) so any of
     * those are not added multiple times to QueryBuilder.
     *
     * @note processJoins() method must be called for joins to actually be added to QueryBuilder. processQueryBuilder()
     *       method calls this method automatically.
     *
     * @see QueryBuilder::leftJoin()
     * @see QueryBuilder::innerJoin()
     *
     * @param string $type Join type; leftJoin, innerJoin or join
     * @param array<int, scalar> $parameters Query builder join parameters
     */
    private function addJoinToQuery(string $type, array $parameters): void
    {
        $comparison = implode('|', $parameters);

        if (!in_array($comparison, self::$processedJoins[$type], true)) {
            self::$joins[$type][] = $parameters;

            self::$processedJoins[$type][] = $comparison;
        }
    }
}
