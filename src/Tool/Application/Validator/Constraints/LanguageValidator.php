<?php

declare(strict_types=1);

namespace App\Tool\Application\Validator\Constraints;

use App\Tool\Application\Service\LocalizationService;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function in_array;

/**
 * @package App\Tool
 */
class LanguageValidator extends ConstraintValidator
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
        if (in_array($value, $this->localization->getLanguages(), true) !== true) {
            $this->context
                ->buildViolation(Language::MESSAGE)
                ->setParameter('{{ language }}', (string)$value)
                ->setCode(Language::INVALID_LANGUAGE)
                ->addViolation();
        }
    }
}
