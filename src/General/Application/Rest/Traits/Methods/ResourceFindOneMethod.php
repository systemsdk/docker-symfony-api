<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\Rest\Traits\RestResourceFindOne as FindOneLifeCycle;
use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @package App\General
 */
trait ResourceFindOneMethod
{
    use FindOneLifeCycle;

    /**
     * {@inheritdoc}
     */
    public function findOne(
        string $id,
        ?bool $throwExceptionIfNotFound = null,
        ?string $entityManagerName = null
    ): ?EntityInterface {
        $throwExceptionIfNotFound ??= false;
        // Before callback method call
        $this->beforeFindOne($id);
        /** @var EntityInterface|null $entity */
        $entity = $this->getRepository()->findAdvanced(id: $id, entityManagerName: $entityManagerName);
        $this->checkThatEntityExists($throwExceptionIfNotFound, $entity);
        // After callback method call
        $this->afterFindOne($id, $entity);

        return $entity;
    }
}
