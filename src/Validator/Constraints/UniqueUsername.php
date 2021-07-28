<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueUsername
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @package App\Validator\Constraints
 */
class UniqueUsername extends Constraint
{
    /**
     * Unique constant for validator constrain
     */
    public const IS_UNIQUE_USERNAME_ERROR = 'ea62740a-4d9b-4a25-9a56-46fb4c3d5fea';

    /**
     * Message for validation error
     */
    public const MESSAGE = 'This username is already taken.';

    /**
     * {@inheritdoc}
     *
     * @var array<string, string>
     */
    protected static $errorNames = [
        self::IS_UNIQUE_USERNAME_ERROR => 'IS_UNIQUE_USERNAME_ERROR',
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
