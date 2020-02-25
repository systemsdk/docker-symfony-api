<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/AbstractGenericMethods.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\Interfaces\RestResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use UnexpectedValueException;

/**
 * Trait AbstractGenericMethods
 *
 * @package App\Rest\Traits\Methods
 */
trait AbstractGenericMethods
{
    /**
     * @param Request $request
     * @param array   $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return RestResourceInterface
     */
    abstract public function getResourceForMethod(Request $request, array $allowedHttpMethods): RestResourceInterface;

    /**
     * @throws UnexpectedValueException
     *
     * @return ResponseHandlerInterface
     */
    abstract public function getResponseHandler(): ResponseHandlerInterface;

    /**
     * Method to process current criteria array.
     *
     * @param mixed[] $criteria
     */
    abstract public function processCriteria(array &$criteria): void;

    /**
     * Method to handle possible REST method trait exception.
     *
     * @param Throwable   $exception
     * @param string|null $id
     *
     * @throws HttpException
     *
     * @return Throwable
     */
    abstract public function handleRestMethodException(Throwable $exception, ?string $id = null): Throwable;
}
