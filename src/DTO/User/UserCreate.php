<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\Entity\User as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserCreate
 *
 * @package App\DTO\User
 */
class UserCreate extends User
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: Entity::PASSWORD_MIN_LENGTH, max: 255)]
    protected string $password = '';
}
