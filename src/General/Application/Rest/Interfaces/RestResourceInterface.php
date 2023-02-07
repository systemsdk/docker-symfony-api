<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Interface RestResourceInterface
 *
 * @package App\General
 */
#[AutoconfigureTag('app.rest.resource')]
#[AutoconfigureTag('app.stopwatch')]
// phpcs:ignore
interface RestResourceInterface extends BaseRestResourceInterface, RestCountResourceInterface, RestCreateResourceInterface, RestDeleteResourceInterface, RestIdsResourceInterface, RestListResourceInterface, RestPatchResourceInterface, RestUpdateResourceInterface, RestFindOneResourceInterface
{
}
