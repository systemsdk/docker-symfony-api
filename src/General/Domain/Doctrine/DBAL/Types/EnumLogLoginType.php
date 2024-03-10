<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine\DBAL\Types;

/**
 * @package App\General
 */
class EnumLogLoginType extends EnumType
{
    final public const string TYPE_FAILURE = 'failure';
    final public const string TYPE_SUCCESS = 'success';

    protected static string $name = Types::ENUM_LOG_LOGIN;

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::TYPE_FAILURE,
        self::TYPE_SUCCESS,
    ];
}
