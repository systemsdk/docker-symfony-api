<?php
declare(strict_types = 1);
/**
 * /src/Validator/Constraints/TimezoneValidator.php
 */

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class TimezoneValidator
 *
 * @package App\Validator\Constraints
 */
class TimezoneValidator extends ConstraintValidator
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
        if (in_array($value, array_column($this->localization->getTimezones(), 'identifier'), true) !== true) {
            $this->context
                ->buildViolation(Timezone::MESSAGE)
                ->setParameter('{{ timezone }}', $value)
                ->setCode(Timezone::INVALID_TIMEZONE)
                ->addViolation();
        }
    }
}
