<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Class Language
 *
 * Usage example;
 *  App\Validator\Constraints\Language()
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Validator\Constraints
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Language extends Constraint
{
    public const INVALID_LANGUAGE = '08bd61cf-ba27-45a3-9916-78c39253833a';
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
