<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

/**
 * Class EnumLocaleType
 *
 * @package App\Doctrine\DBAL\Types
 */
class EnumLocaleType extends EnumType
{
    public const LOCALE_EN = 'en';
    public const LOCALE_RU = 'ru';
    public const LOCALE_FI = 'fi';

    protected static string $name = Types::ENUM_LOCALE;

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::LOCALE_EN,
        self::LOCALE_RU,
        self::LOCALE_FI,
    ];
}
