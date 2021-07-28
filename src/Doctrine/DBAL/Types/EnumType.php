<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

use function array_map;
use function implode;
use function in_array;
use function is_string;
use function sprintf;

/**
 * Class EnumType
 *
 * @package App\Doctrine\DBAL\Types
 */
abstract class EnumType extends Type
{
    protected static string $name;

    /**
     * @var array<int, string>
     */
    protected static array $values = [];

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return static::$values;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $iterator = static fn (string $value): string => "'" . $value . "'";

        return 'ENUM(' . implode(', ', array_map($iterator, self::getValues())) . ')';
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $value = (string)parent::convertToDatabaseValue(is_string($value) ? $value : '', $platform);

        if (!in_array($value, static::$values, true)) {
            $message = sprintf(
                "Invalid '%s' value",
                $this->getName()
            );

            throw new InvalidArgumentException($message);
        }

        return $value;
    }

    public function getName(): string
    {
        return static::$name;
    }

    /**
     * If this Doctrine Type maps to an already mapped database type, reverse schema engineering can't take them apart.
     * You need to mark one of those types as commented, which will have Doctrine use an SQL comment to type hint the
     * actual Doctrine Type.
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        parent::requiresSQLCommentHint($platform);

        return true;
    }
}
