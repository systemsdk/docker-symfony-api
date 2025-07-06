<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine\DBAL\Types;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Override;

use function array_map;
use function gettype;
use function implode;
use function in_array;
use function is_string;
use function sprintf;

/**
 * @package App\General
 */
abstract class EnumType extends Type
{
    protected static string $name;

    /**
     * @psalm-var class-string<DatabaseEnumInterface&BackedEnum>
     */
    protected static string $enum;

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return static::$enum::getValues();
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     */
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $enumDefinition = implode(
            ', ',
            array_map(static fn (string $value): string => "'" . $value . "'", static::getValues()),
        );

        return 'ENUM(' . $enumDefinition . ')';
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (is_string($value) && in_array($value, static::$enum::getValues(), true)) {
            $value = static::$enum::from($value);
        }

        if (!in_array($value, static::$enum::cases(), true)) {
            $message = sprintf(
                "Invalid '%s' value '%s'",
                static::$name,
                is_string($value) ? $value : gettype($value),
            );

            throw new InvalidArgumentException($message);
        }

        return (string)parent::convertToDatabaseValue($value->value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): DatabaseEnumInterface
    {
        $value = (string)parent::convertToPHPValue($value, $platform);
        $enum = static::$enum::tryFrom($value);

        if ($enum !== null) {
            return $enum;
        }

        throw ValueNotConvertible::new(
            gettype($value),
            static::$name,
            'One of: "' . implode('", "', static::getValues()) . '"'
        );
    }
}
