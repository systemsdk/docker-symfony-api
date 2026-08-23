<?php

declare(strict_types=1);

namespace App\General\Domain\Enum\Interfaces;

/**
 * @package App\General
 */
interface LabeledEnumInterface
{
    public function label(): string;
}
