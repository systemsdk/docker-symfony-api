<?php
declare(strict_types = 1);
/**
 * /src/App/Validator/Constraints/UniqueEmailValidator.php
 */

namespace App\Validator\Constraints;

use App\Entity\Interfaces\UserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueEmailValidator
 *
 * @package App\Validator\Constraints
 */
class UniqueEmailValidator extends ConstraintValidator
{
    private UserRepository $repository;

    /**
     * Constructor
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param UserInterface|mixed    $value      The value that should be validated
     * @param Constraint|UniqueEmail $constraint The constraint for the validation
     *
     * @throws NonUniqueResultException
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value instanceof UserInterface
            && !$this->repository->isEmailAvailable($value->getEmail(), $value->getId())
        ) {
            $this->context
                ->buildViolation(UniqueEmail::MESSAGE)
                ->setCode(UniqueEmail::IS_UNIQUE_EMAIL_ERROR)
                ->addViolation();
        }
    }
}
