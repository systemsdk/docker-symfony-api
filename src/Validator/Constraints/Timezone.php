<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Class Timezone
 *
 * Usage example;
 *  App\Validator\Constraints\Timezone()
 *
 * Just add that to your property as an annotation and you're good to go.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Validator\Constraints
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Timezone extends Constraint
{
    public const INVALID_TIMEZONE = '1f8dd2a3-5b61-43ca-a6b2-af553f86ac17';
    public const MESSAGE = 'This timezone "{{ timezone }}" is not valid.';

    /**
     * {@inheritdoc}
     *
     * @var array<string, string>
     */
    protected static $errorNames = [
        self::INVALID_TIMEZONE => 'INVALID_TIMEZONE',
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
