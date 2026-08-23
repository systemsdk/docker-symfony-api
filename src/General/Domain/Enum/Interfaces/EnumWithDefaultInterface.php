<?php

declare(strict_types=1);

namespace App\General\Domain\Enum\Interfaces;

/**
 * @package App\General
 */
interface EnumWithDefaultInterface
{
    public static function getDefault(): self;
}
