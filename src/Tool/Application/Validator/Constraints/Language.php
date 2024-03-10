<?php

declare(strict_types=1);

namespace App\Tool\Application\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Usage example;
 *  App\Tool\Application\Validator\Constraints\Language()
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Tool
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Language extends Constraint
{
    final public const string INVALID_LANGUAGE = '08bd61cf-ba27-45a3-9916-78c39253833a';
    final public const string MESSAGE = 'This language "{{ language }}" is not valid.';

    /**
     * {@inheritdoc}
     *
     * @psalm-var array<string, string>
     */
    protected const array ERROR_NAMES = [
        self::INVALID_LANGUAGE => 'INVALID_LANGUAGE',
    ];
}
