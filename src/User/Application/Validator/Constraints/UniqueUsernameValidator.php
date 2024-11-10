<?php

declare(strict_types=1);

namespace App\User\Application\Validator\Constraints;

use App\User\Application\DTO\User\User as UserDto;
use App\User\Domain\Entity\Interfaces\UserInterface;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @package App\User
 */
class UniqueUsernameValidator extends ConstraintValidator
{
    /**
     * @param \App\User\Infrastructure\Repository\UserRepository $repository
     */
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (
            ($value instanceof UserInterface || $value instanceof UserDto)
            && !$this->repository->isUsernameAvailable($value->getUsername(), $value->getId())
        ) {
            $this->context
                ->buildViolation(UniqueUsername::MESSAGE)
                ->setCode(UniqueUsername::IS_UNIQUE_USERNAME_ERROR)
                ->addViolation();
        }
    }
}
