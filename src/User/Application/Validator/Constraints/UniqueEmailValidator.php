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
class UniqueEmailValidator extends ConstraintValidator
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
            && !$this->repository->isEmailAvailable($value->getEmail(), $value->getId())
        ) {
            $this->context
                ->buildViolation(UniqueEmail::MESSAGE)
                ->setCode(UniqueEmail::IS_UNIQUE_EMAIL_ERROR)
                ->addViolation();
        }
    }
}
