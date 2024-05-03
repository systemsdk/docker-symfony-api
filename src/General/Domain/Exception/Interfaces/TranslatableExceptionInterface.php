<?php

declare(strict_types=1);

namespace App\General\Domain\Exception\Interfaces;

use Throwable;

/**
 * @package App\General
 */
interface TranslatableExceptionInterface extends Throwable
{
    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    public function getDomain(): ?string;
}
