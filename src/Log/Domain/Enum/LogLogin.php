<?php

declare(strict_types=1);

namespace App\Log\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\Log
 */
enum LogLogin: string implements DatabaseEnumInterface
{
    use GetValues;

    case FAILURE = 'failure';
    case SUCCESS = 'success';
}
