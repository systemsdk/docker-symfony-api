<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\Traits\RestResourceUpdate as UpdateLifeCycle;
use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @package App\General
 */
trait ResourceUpdateMethod
{
    use UpdateLifeCycle;

    /**
     * {@inheritdoc}
     */
    public function update(
        string $id,
        RestDtoInterface $dto,
        ?bool $flush = null,
        ?bool $skipValidation = null,
        ?string $entityManagerName = null
    ): EntityInterface {
        $flush ??= true;
        $skipValidation ??= false;
        // Fetch entity
        $entity = $this->getEntity($id, $entityManagerName);
        /**
         * Determine used dto class and create new instance of that and load entity to that. And after that patch
         * that dto with given partial OR whole dto class.
         */
        $restDto = $this->getDtoForEntity(
            id: $id,
            dtoClass: $dto::class,
            dto: $dto,
            entityManagerName: $entityManagerName
        );
        // Before callback method call
        $this->beforeUpdate($id, $restDto, $entity);
        // Validate DTO
        $this->validateDto($restDto, $skipValidation);
        // Create or update entity
        $this->persistEntity($entity, $restDto, $flush, $skipValidation, $entityManagerName);
        // After callback method call
        $this->afterUpdate($id, $restDto, $entity);

        return $entity;
    }
}
