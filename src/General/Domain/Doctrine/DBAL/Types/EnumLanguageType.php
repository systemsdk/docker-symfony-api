<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine\DBAL\Types;

/**
 * Class EnumLanguageType
 *
 * @package App\General
 */
class EnumLanguageType extends EnumType
{
    public const LANGUAGE_EN = 'en';
    public const LANGUAGE_RU = 'ru';
    public const LANGUAGE_UA = 'ua';
    public const LANGUAGE_FI = 'fi';

    protected static string $name = Types::ENUM_LANGUAGE;

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::LANGUAGE_EN,
        self::LANGUAGE_RU,
        self::LANGUAGE_UA,
        self::LANGUAGE_FI,
    ];
}
