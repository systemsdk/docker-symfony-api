<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestFindOneResourceInterface extends RestSmallResourceInterface
{
    /**
     * Generic findOne method to return single item from database. Return value is single entity from specified
     * repository.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @psalm-return (
     *     $throwExceptionIfNotFound is true
     *     ? EntityInterface
     *     : EntityInterface|null
     * )
     *
     * @throws Throwable
     */
    public function findOne(
        string $id,
        ?bool $throwExceptionIfNotFound = null,
        ?string $entityManagerName = null
    ): ?EntityInterface;

    /**
     * Generic findOneBy method to return single item from database by given criteria. Return value is single entity
     * from specified repository or null if entity was not found.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @param array<int|string, string|array<mixed>> $criteria
     * @param array<int, string>|null $orderBy
     *
     * @psalm-return (
     *     $throwExceptionIfNotFound is true
     *     ? EntityInterface
     *     : EntityInterface|null
     * )
     *
     * @throws Throwable
     */
    public function findOneBy(
        array $criteria,
        ?array $orderBy = null,
        ?bool $throwExceptionIfNotFound = null,
        ?string $entityManagerName = null
    ): ?EntityInterface;
}
