<?php

declare(strict_types=1);

namespace App\User\Domain\Entity\Interfaces;

/**
 * Interface UserInterface
 *
 * @package App\User
 */
interface UserInterface
{
    public function getId(): string;
    public function getUsername(): string;
    public function getEmail(): string;
}
