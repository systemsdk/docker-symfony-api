<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine\DBAL\Types;

/**
 * Class EnumLogLoginType
 *
 * @package App\General
 */
class EnumLogLoginType extends EnumType
{
    final public const TYPE_FAILURE = 'failure';
    final public const TYPE_SUCCESS = 'success';

    protected static string $name = Types::ENUM_LOG_LOGIN;

    /**
     * @var array<int, string>
     */
    protected static array $values = [
        self::TYPE_FAILURE,
        self::TYPE_SUCCESS,
    ];
}
