<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/RestResourceCount.php
 */

namespace App\Rest\Traits;

/**
 * Trait RestResourceCount
 *
 * @SuppressWarnings("unused")
 *
 * @package App\Rest\Traits
 */
trait RestResourceCount
{
    /**
     * Before lifecycle method for count method.
     *
     * @param array $criteria
     * @param array $search
     */
    public function beforeCount(array &$criteria, array &$search): void
    {
    }

    /**
     * Before lifecycle method for count method.
     *
     * @param array $criteria
     * @param array $search
     * @param int     $count
     */
    public function afterCount(array &$criteria, array &$search, int &$count): void
    {
    }
}
