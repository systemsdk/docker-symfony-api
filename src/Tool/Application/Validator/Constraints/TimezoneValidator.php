<?php

declare(strict_types=1);

namespace App\Tool\Application\Validator\Constraints;

use App\Tool\Application\Service\LocalizationService;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function array_column;
use function in_array;
use function is_string;

/**
 * @package App\Tool
 */
class TimezoneValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LocalizationService $localization,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (
            is_string($value)
            && !in_array($value, array_column($this->localization->getTimezones(), 'identifier'), true)
        ) {
            $this->context
                ->buildViolation(Timezone::MESSAGE)
                ->setParameter('{{ timezone }}', $value)
                ->setCode(Timezone::INVALID_TIMEZONE)
                ->addViolation();
        }
    }
}
