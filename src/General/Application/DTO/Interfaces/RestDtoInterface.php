<?php

declare(strict_types=1);

namespace App\General\Application\DTO\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestDtoInterface
{
    public function setId(string $id): self;

    /**
     * Getter method for visited setters. This is needed for dto patching.
     *
     * @return array<int, string>
     */
    public function getVisited(): array;

    /**
     * Setter for visited data. This is needed for dto patching.
     */
    public function setVisited(string $property): self;

    /**
     * Method to load DTO data from specified entity.
     */
    public function load(EntityInterface $entity): self;

    /**
     * Method to update specified entity with DTO data.
     */
    public function update(EntityInterface $entity): EntityInterface;

    /**
     * Method to patch current dto with another one.
     *
     * @throws Throwable
     */
    public function patch(self $dto): self;
}
