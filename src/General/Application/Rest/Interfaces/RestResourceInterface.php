<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

/**
 * Interface RestResourceInterface
 *
 * @package App\General
 */
interface RestResourceInterface extends
    BaseRestResourceInterface,
    RestCountResourceInterface,
    RestCreateResourceInterface,
    RestDeleteResourceInterface,
    RestIdsResourceInterface,
    RestListResourceInterface,
    RestPatchResourceInterface,
    RestUpdateResourceInterface,
    RestFindOneResourceInterface
{
}
