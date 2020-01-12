<?php
declare(strict_types = 1);
/**
 * /src/Rest/DTO/User/UserCreate.php
 */

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User as Entity;

/**
 * Class UserCreate
 *
 * @package App\DTO\User
 */
class UserCreate extends User
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = Entity::PASSWORD_MIN_LENGTH, max = 255, allowEmptyString="false")
     */
    protected string $password = '';
}
