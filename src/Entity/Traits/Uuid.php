<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Rest\UuidHelper;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * Trait Uuid
 *
 * @package App\Entity\Traits
 */
trait Uuid
{
    public function getUuid(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @throws Throwable
     */
    protected function createUuid(): UuidInterface
    {
        return UuidHelper::getFactory()->uuid1();
    }
}
