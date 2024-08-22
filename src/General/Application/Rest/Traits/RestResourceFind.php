<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits;

use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @SuppressWarnings("unused")
 *
 * @package App\General
 */
trait RestResourceFind
{
    /**
     * Before lifecycle method for find method.
     *
     * @param mixed[] $criteria
     * @param mixed[] $orderBy
     * @param mixed[] $search
     */
    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
    }

    /**
     * After lifecycle method for find method.
     *
     * Notes: If you make changes to entities in this lifecycle method by default it will be saved on end of current
     *          request. To prevent this you need to clone each entity and use those.
     *
     * @param mixed[] $criteria
     * @param mixed[] $orderBy
     * @param mixed[] $search
     * @param EntityInterface[] $entities
     */
    public function afterFind(
        array &$criteria,
        array &$orderBy,
        ?int &$limit,
        ?int &$offset,
        array &$search,
        array &$entities
    ): void {
    }
}
