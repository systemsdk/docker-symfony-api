<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

use function array_map;
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

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getName(): string
    {
        return static::$name;
    }
}
