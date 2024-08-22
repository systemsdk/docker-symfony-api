<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestUpdateResourceInterface extends RestSaveResourceInterface
{
    /**
     * Generic method to update specified entity with new data.
     *
     * @codeCoverageIgnore This is needed because variables are multiline
     *
     * @throws Throwable
     */
    public function update(
        string $id,
        RestDtoInterface $dto,
        ?bool $flush = null,
        ?bool $skipValidation = null,
        ?string $entityManagerName = null
    ): EntityInterface;
}
