<?php

declare(strict_types=1);

namespace App\General\Application\Exception;

use App\General\Application\Exception\Interfaces\ClientErrorInterface;
use App\General\Application\Exception\Models\ValidatorError;
use App\General\Domain\Utils\JSON;
use JsonException;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException as BaseValidatorException;

use function array_map;
use function iterator_to_array;

/**
 * @package App\General
 */
class ValidatorException extends BaseValidatorException implements ClientErrorInterface
{
    /**
     * @throws JsonException
     */
    public function __construct(string $target, ConstraintViolationListInterface $errors)
    {
        parent::__construct(
            JSON::encode(
                array_map(
                    static fn (ConstraintViolationInterface $error): ValidatorError =>
                        new ValidatorError($error, $target),
                    iterator_to_array($errors),
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
