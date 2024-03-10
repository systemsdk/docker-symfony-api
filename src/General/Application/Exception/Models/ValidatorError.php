<?php

declare(strict_types=1);

namespace App\General\Application\Exception\Models;

use Stringable;
use Symfony\Component\Validator\ConstraintViolationInterface;

use function str_replace;

/**
 * @package App\General
 */
class ValidatorError
{
    public string | Stringable $message;
    public string $propertyPath;
    public string $target;
    public string | null $code;

    public function __construct(ConstraintViolationInterface $error, string $target)
    {
        $this->message = $error->getMessage();
        $this->propertyPath = $error->getPropertyPath();
        $this->target = str_replace('\\', '.', $target);
        $this->code = $error->getCode();
    }
}
