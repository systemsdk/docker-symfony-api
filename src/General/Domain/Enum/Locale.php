<?php

declare(strict_types=1);

namespace App\General\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Interfaces\EnumWithDefaultInterface;
use App\General\Domain\Enum\Traits\GetValues;
use Override;

/**
 * Locale
 *
 * @package App\General
 */
enum Locale: string implements DatabaseEnumInterface, EnumWithDefaultInterface
{
    use GetValues;

    case EN = 'en';
    case RU = 'ru';
    case UA = 'ua';
    case FI = 'fi';

    #[Override]
    public static function getDefault(): self
    {
        return self::EN;
    }
}
