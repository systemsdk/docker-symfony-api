<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\Traits\RestResourceSave as SaveLifeCycle;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
trait ResourceSaveMethod
{
    use SaveLifeCycle;

    /**
     * {@inheritdoc}
     */
    public function save(
        EntityInterface $entity,
        ?bool $flush = null,
        ?bool $skipValidation = null,
        ?string $entityManagerName = null
    ): EntityInterface {
        $flush ??= true;
        $skipValidation ??= false;
        // Before callback method call
        $this->beforeSave($entity);
        // Validate current entity
        $this->validateEntity($entity, $skipValidation);
        // Persist on database
        $this->getRepository()->save($entity, $flush, $entityManagerName);
        // After callback method call
        $this->afterSave($entity);

        return $entity;
    }

    /**
     * Helper method to set data to specified entity and store it to database.
     *
     * @throws Throwable
     */
    protected function persistEntity(
        EntityInterface $entity,
        RestDtoInterface $dto,
        bool $flush,
        bool $skipValidation,
        ?string $entityManagerName
    ): void {
        // Update entity according to DTO current state
        $dto->update($entity);
        // And save current entity
        $this->save($entity, $flush, $skipValidation, $entityManagerName);
    }
}
