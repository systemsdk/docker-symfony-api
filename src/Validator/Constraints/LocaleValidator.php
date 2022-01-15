<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function in_array;

/**
 * Class LocaleValidator
 *
 * @package App\Validator\Constraints
 */
class LocaleValidator extends ConstraintValidator
{
    public function __construct(
        private LocalizationService $localization,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (in_array($value, $this->localization->getLocales(), true) !== true) {
            $this->context
                ->buildViolation(Locale::MESSAGE)
                ->setParameter('{{ locale }}', (string)$value)
                ->setCode(Locale::INVALID_LOCALE)
                ->addViolation();
        }
    }
}
