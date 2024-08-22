<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestSaveResourceInterface extends RestSmallResourceInterface
{
    /**
     * Generic method to save given entity to specified repository. Return value is created entity.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @throws Throwable
     */
    public function save(
        EntityInterface $entity,
        ?bool $flush = null,
        ?bool $skipValidation = null,
        ?string $entityManagerName = null
    ): EntityInterface;
}
