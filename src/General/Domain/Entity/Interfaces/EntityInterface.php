<?php

declare(strict_types=1);

namespace App\General\Domain\Entity\Interfaces;

use DateTimeImmutable;

/**
 * Interface EntityInterface
 *
 * @package App\General
 */
interface EntityInterface
{
    public function getId(): string;
    public function getCreatedAt(): ?DateTimeImmutable;
}
