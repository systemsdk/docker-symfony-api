<?php

declare(strict_types=1);

namespace App\General\Domain\Enum\Interfaces;

use BackedEnum;

/**
 * Enum StringEnumInterface
 *
 * @package App\General
 */
interface StringEnumInterface extends BackedEnum
{
    /**
     * @return array<int, string>
     */
    public static function getValues(): array;
}
