<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits;

/**
 * @SuppressWarnings("unused")
 *
 * @package App\General
 */
trait RestResourceIds
{
    /**
     * Before lifecycle method for ids method.
     *
     * @param array<int|string, string|array<mixed>> $criteria
     * @param array<string, array<int, string>|string> $search
     */
    public function beforeIds(array &$criteria, array &$search): void
    {
    }

    /**
     * Before lifecycle method for ids method.
     *
     * @param array<int|string, string|array<mixed>> $criteria
     * @param array<string, array<int, string>|string> $search
     * @param array<int, string> $ids
     */
    public function afterIds(array &$criteria, array &$search, array &$ids): void
    {
    }
}
