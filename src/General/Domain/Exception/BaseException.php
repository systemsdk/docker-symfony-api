<?php

declare(strict_types=1);

namespace App\General\Domain\Exception;

use App\General\Domain\Exception\Interfaces\ExceptionInterface;
use Exception;

/**
 * @package App\General
 */
abstract class BaseException extends Exception implements ExceptionInterface
{
}
