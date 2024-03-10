<?php

declare(strict_types=1);

namespace App\General\Application\Rest;

use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use App\General\Application\Rest\Traits\RestResourceBaseMethods;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;

/**
 * @package App\General
 */
abstract class RestSmallResource implements RestSmallResourceInterface
{
    use RestResourceBaseMethods;

    public function __construct(
        protected readonly BaseRepositoryInterface $repository,
    ) {
    }
}
