<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\Rest\Traits\RestResourceFind as FindLifeCycle;
use App\General\Domain\Entity\Interfaces\EntityInterface;

use function array_keys;

/**
 * @package App\General
 */
trait ResourceFindMethod
{
    use FindLifeCycle;

    /**
     * {@inheritDoc}
     *
     * @return array<int, EntityInterface>
     */
    public function find(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null
    ): array {
        $criteria ??= [];
        $orderBy ??= [];
        $search ??= [];
        // Before callback method call
        $this->beforeFind($criteria, $orderBy, $limit, $offset, $search);
        // Fetch data
        $entities = $this->getRepository()->findByAdvanced(
            $criteria,
            $this->normalizeOrderBy($orderBy),
            $limit,
            $offset,
            $search,
            $entityManagerName
        );
        // After callback method call
        $this->afterFind($criteria, $orderBy, $limit, $offset, $search, $entities);

        return $entities;
    }

    /**
     * @param mixed[] $orderBy
     *
     * @return array<string, string>
     */
    private function normalizeOrderBy(array $orderBy): array
    {
        $normalized = [];

        foreach (array_keys($orderBy) as $key) {
            $normalized[(string)$key] = (string)$orderBy[$key];
        }

        return $normalized;
    }
}
