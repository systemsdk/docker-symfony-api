<?php
declare(strict_types = 1);
/**
 * /src/Validator/Constraints/LocaleValidator.php
 */

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class LocaleValidator
 *
 * @package App\Validator\Constraints
 */
class LocaleValidator extends ConstraintValidator
{
    private LocalizationService $localization;

    /**
     * Constructor
     *
     * @param LocalizationService $localization
     */
    public function __construct(LocalizationService $localization)
    {
        $this->localization = $localization;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (in_array($value, $this->localization->getLocales(), true) !== true) {
            if (!is_string($value)) {
                $value = $value->getLocale();
            }

            $this->context
                ->buildViolation(Locale::MESSAGE)
                ->setParameter('{{ locale }}', $value)
                ->setCode(Locale::INVALID_LOCALE)
                ->addViolation();
        }
    }
}
