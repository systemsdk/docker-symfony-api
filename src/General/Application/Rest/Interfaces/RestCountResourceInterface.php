<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use Throwable;

/**
 * @package App\General
 */
interface RestCountResourceInterface extends RestSmallResourceInterface
{
    /**
     * Generic count method to return entity count for specified criteria and search terms.
     *
     * @param array<int|string, string|array<mixed>>|null $criteria
     * @param array<string, string>|null $search
     *
     * @throws Throwable
     */
    public function count(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): int;
}
