<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

/**
 * Class EnumLanguageType
 *
 * @package App\Doctrine\DBAL\Types
 */
class EnumLanguageType extends EnumType
{
    public const LANGUAGE_EN = 'en';
    public const LANGUAGE_RU = 'ru';

    protected static string $name = 'EnumLanguage';

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::LANGUAGE_EN,
        self::LANGUAGE_RU,
    ];
}
