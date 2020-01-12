<?php
declare(strict_types = 1);
/**
 * /src/Entity/Traits/Uuid.php
 */

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
    /**
     * @return UuidInterface
     *
     * @throws Throwable
     */
    protected function getUuid(): UuidInterface
    {
        return UuidHelper::getFactory()->uuid1();
    }
}
