<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Domain\Entity\User as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User
 */
class UserCreate extends User
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: Entity::PASSWORD_MIN_LENGTH, max: 255)]
    protected string $password = '';
}
