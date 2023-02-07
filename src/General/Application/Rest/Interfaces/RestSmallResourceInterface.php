<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Interface RestSmallResourceInterface
 *
 * @package App\General
 */
#[AutoconfigureTag('app.rest.resource')]
#[AutoconfigureTag('app.stopwatch')]
interface RestSmallResourceInterface extends BaseRestResourceInterface
{
}
