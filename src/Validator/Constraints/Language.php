<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Language
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Validator\Constraints
 */
class Language extends Constraint
{
    /**
     * Unique constant for validator constrain
     */
    public const INVALID_LANGUAGE = '08bd61cf-ba27-45a3-9916-78c39253833a';

    /**
     * Message for validation error
     */
    public const MESSAGE = 'This language "{{ language }}" is not valid.';

    /**
     * {@inheritdoc}
     *
     * @var array<string, string>
     */
    protected static $errorNames = [
        self::INVALID_LANGUAGE => 'INVALID_LANGUAGE',
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
