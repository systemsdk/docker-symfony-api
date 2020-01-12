<?php
declare(strict_types = 1);
/**
 * /src/DTO/Interfaces/RestDtoInterface.php
 */

namespace App\DTO\Interfaces;

use App\Entity\Interfaces\EntityInterface;
use BadMethodCallException;
use LogicException;

/**
 * Interface RestDtoInterface
 *
 * @package App\DTO\Interfaces
 */
interface RestDtoInterface
{
    /**
     * @param string $id
     *
     * @return RestDtoInterface
     */
    public function setId(string $id): self;

    /**
     * Getter method for visited setters. This is needed for dto patching.
     *
     * @return array
     */
    public function getVisited(): array;

    /**
     * Setter for visited data. This is needed for dto patching.
     *
     * @param string $property
     *
     * @return RestDtoInterface
     */
    public function setVisited(string $property): self;

    /**
     * Method to load DTO data from specified entity.
     *
     * @param EntityInterface $entity
     *
     * @return RestDtoInterface
     */
    public function load(EntityInterface $entity): self;

    /**
     * Method to update specified entity with DTO data.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function update(EntityInterface $entity): EntityInterface;

    /**
     * Method to patch current dto with another one.
     *
     * @param RestDtoInterface $dto
     *
     * @throws LogicException|BadMethodCallException
     *
     * @return RestDtoInterface
     */
    public function patch(self $dto): self;
}
