<?php
declare(strict_types = 1);
/**
 * /src/Exception/ValidatorException.php
 */

namespace App\Exception;

use App\Exception\Interfaces\ClientErrorInterface;
use App\Utils\JSON;
use JsonException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException as BaseValidatorException;

/**
 * Class ValidatorException
 *
 * @package App\Exception
 */
class ValidatorException extends BaseValidatorException implements ClientErrorInterface
{
    /**
     * Constructor
     *
     * @param string                           $target
     * @param ConstraintViolationListInterface $errors
     *
     * @throws JsonException
     */
    public function __construct(string $target, ConstraintViolationListInterface $errors)
    {
        $output = [];

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $output[] = [
                'message' => $error->getMessage(),
                'propertyPath' => $error->getPropertyPath(),
                'target' => str_replace('\\', '.', $target),
                'code' => $error->getCode(),
            ];
        }

        parent::__construct(JSON::encode($output));
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
