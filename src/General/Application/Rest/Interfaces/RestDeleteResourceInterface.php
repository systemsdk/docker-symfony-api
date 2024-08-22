<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use Throwable;

/**
 * @package App\General
 */
interface RestDeleteResourceInterface extends RestSmallResourceInterface
{
    /**
     * Generic method to delete specified entity from database.
     *
     * @throws Throwable
     */
    public function delete(string $id, ?bool $flush = null, ?string $entityManagerName = null): EntityInterface;
}
