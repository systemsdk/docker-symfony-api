<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function array_column;
use function in_array;
use function is_string;

/**
 * Class TimezoneValidator
 *
 * @package App\Validator\Constraints
 */
class TimezoneValidator extends ConstraintValidator
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
        if (in_array($value, array_column($this->localization->getTimezones(), 'identifier'), true) !== true) {
            $this->context
                ->buildViolation(Timezone::MESSAGE)
                ->setParameter('{{ timezone }}', !is_string($value) ? $value->getTimezone() : $value)
                ->setCode(Timezone::INVALID_TIMEZONE)
                ->addViolation();
        }
    }
}
