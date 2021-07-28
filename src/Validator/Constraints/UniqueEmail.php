<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueEmail
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @package App\Validator\Constraints
 */
class UniqueEmail extends Constraint
{
    /**
     * Unique constant for validator constrain
     */
    public const IS_UNIQUE_EMAIL_ERROR = 'd487278d-8b13-4da0-b4cc-f862e6e99af6';

    /**
     * Message for validation error
     */
    public const MESSAGE = 'This email is already taken.';

    /**
     * {@inheritdoc}
     *
     * @var array<string, string>
     */
    protected static $errorNames = [
        self::IS_UNIQUE_EMAIL_ERROR => 'IS_UNIQUE_EMAIL_ERROR',
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
