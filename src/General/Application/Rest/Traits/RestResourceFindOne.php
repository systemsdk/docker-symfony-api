<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits;

use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @SuppressWarnings("unused")
 *
 * @package App\General
 */
trait RestResourceFindOne
{
    /**
     * Before lifecycle method for findOne method.
     */
    public function beforeFindOne(string &$id): void
    {
    }

    /**
     * After lifecycle method for findOne method.
     *
     * Notes: If you make changes to entity in this lifecycle method by default it will be saved on end of current
     *          request. To prevent this you need to detach current entity from entity manager.
     *
     *          Also note that if you've made some changes to entity and you eg. throw an exception within this method
     *          your entity will be saved if it has eg Blameable / Timestampable traits attached.
     */
    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
    }
}
