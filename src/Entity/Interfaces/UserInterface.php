<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

/**
 * Interface UserInterface
 *
 * @package App\Entity\Interfaces
 */
interface UserInterface
{
    public function getId(): string;

    public function getUsername(): string;

    public function getEmail(): string;
}
