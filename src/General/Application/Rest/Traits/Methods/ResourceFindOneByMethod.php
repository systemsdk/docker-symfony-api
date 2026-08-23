<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\Rest\Traits\RestResourceFindOneBy as FindOneByLifeCycle;
use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @package App\General
 */
trait ResourceFindOneByMethod
{
    use FindOneByLifeCycle;

    /**
     * {@inheritDoc}
     */
    public function findOneBy(
        array $criteria,
        ?array $orderBy = null,
        ?bool $throwExceptionIfNotFound = null,
        ?string $entityManagerName = null
    ): ?EntityInterface {
        $orderBy ??= [];
        $throwExceptionIfNotFound ??= false;
        // Before callback method call
        $this->beforeFindOneBy($criteria, $orderBy);
        /** @var EntityInterface|null $entity */
        $entity = $this->getRepository()->findOneBy($this->normalizeCriteria($criteria), $orderBy, $entityManagerName);
        $this->checkThatEntityExists($throwExceptionIfNotFound, $entity);
        // After callback method call
        $this->afterFindOneBy($criteria, $orderBy, $entity);

        return $entity;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return array<string, mixed>
     */
    private function normalizeCriteria(array $criteria): array
    {
        if ($criteria === []) {
            return [];
        }

        $normalized = [];

        foreach ($criteria as $key => $value) {
            $normalized[(string)$key] = $value;
        }

        return $normalized;
    }
}
