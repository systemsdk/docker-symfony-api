<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits;

/**
 * @SuppressWarnings("unused")
 *
 * @package App\General
 */
trait RestResourceCount
{
    /**
     * Before lifecycle method for count method.
     *
     * @param mixed[] $criteria
     * @param mixed[] $search
     */
    public function beforeCount(array &$criteria, array &$search): void
    {
    }

    /**
     * Before lifecycle method for count method.
     *
     * @param mixed[] $criteria
     * @param mixed[] $search
     */
    public function afterCount(array &$criteria, array &$search, int &$count): void
    {
    }
}
