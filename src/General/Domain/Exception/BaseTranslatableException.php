<?php

declare(strict_types=1);

namespace App\General\Domain\Exception;

use App\General\Domain\Exception\Interfaces\TranslatableExceptionInterface;

/**
 * @package App\General
 */
abstract class BaseTranslatableException extends BaseException implements TranslatableExceptionInterface
{
    public function getParameters(): array
    {
        return [];
    }

    public function getDomain(): ?string
    {
        return null;
    }
}
