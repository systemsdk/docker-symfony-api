<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use UnexpectedValueException;

/**
 * Interface BaseRestResourceInterface
 *
 * @package App\General
 */
interface BaseRestResourceInterface
{
    /**
     * Getter method for serializer context.
     *
     * @return array<int|string, mixed>
     */
    public function getSerializerContext(): array;

    /**
     * Getter method for entity repository.
     *
     * @throws Throwable
     */
    public function getRepository(): BaseRepositoryInterface;

    /**
     * Setter method for repository.
     */
    public function setRepository(BaseRepositoryInterface $repository): self;

    /**
     * Getter for used validator.
     */
    public function getValidator(): ValidatorInterface;

    /**
     * Setter for used validator.
     */
    public function setValidator(ValidatorInterface $validator): self;

    /**
     * Getter method for used DTO class for this REST service.
     *
     * @throws UnexpectedValueException
     */
    public function getDtoClass(): string;

    /**
     * Setter for used DTO class.
     */
    public function setDtoClass(string $dtoClass): self;

    /**
     * Getter method for current entity name.
     *
     * @throws Throwable
     */
    public function getEntityName(): string;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @throws Throwable
     */
    public function getReference(string $id, ?string $entityManagerName = null): ?object;

    /**
     * Getter method for all associations that current entity contains.
     *
     * @return array<int, string>
     *
     * @throws Throwable
     */
    public function getAssociations(?string $entityManagerName = null): array;

    /**
     * Getter method DTO class with loaded entity data.
     *
     * @throws Throwable
     */
    public function getDtoForEntity(
        string $id,
        string $dtoClass,
        RestDtoInterface $dto,
        ?bool $patch = null,
        ?string $entityManagerName = null
    ): RestDtoInterface;
}
