<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use Throwable;

/**
 * @package App\General
 */
interface RestIdsResourceInterface extends RestSmallResourceInterface
{
    /**
     * Generic ids method to return an array of id values from database. Return value is an array of specified
     * repository entity id values.
     *
     * @param array<int|string, string|array<mixed>>|null $criteria
     * @param array<string, string>|null $search
     *
     * @return array<int, string>
     *
     * @throws Throwable
     */
    public function getIds(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): array;
}
