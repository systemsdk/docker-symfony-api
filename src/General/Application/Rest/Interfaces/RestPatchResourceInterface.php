<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestPatchResourceInterface extends RestSaveResourceInterface
{
    /**
     * Generic method to patch specified entity with new data.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @throws Throwable
     */
    public function patch(
        string $id,
        RestDtoInterface $dto,
        ?bool $flush = null,
        ?bool $skipValidation = null,
        ?string $entityManagerName = null
    ): EntityInterface;
}
