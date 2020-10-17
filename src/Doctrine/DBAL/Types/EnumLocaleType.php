<?php
declare(strict_types = 1);
/**
 * /src/Doctrine/DBAL/Types/EnumLocaleType.php
 */

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

    protected static string $name = 'EnumLocale';

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::LOCALE_EN,
        self::LOCALE_RU,
    ];
}
