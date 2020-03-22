<?php
declare(strict_types = 1);
/**
 * /src/Rest/Interfaces/ControllerInterface.php
 */

namespace App\Rest\Interfaces;

use App\Rest\Controller;
use App\Rest\ResponseHandler;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use UnexpectedValueException;

/**
 * Interface ControllerInterface
 *
 * @package App\Rest\Interfaces
 */
interface ControllerInterface
{
    /**
     * Setter method for `resource` service.
     *
     * @param RestResourceInterface $resource
     *
     * @return ControllerInterface|Controller|self
     */
    public function setResource(RestResourceInterface $resource);

    /**
     * Getter method for `resource` service.
     *
     * @throws UnexpectedValueException
     *
     * @return RestResourceInterface
     */
    public function getResource(): RestResourceInterface;

    /**
     * Setter method for `ResponseHandler` service, this is called by Symfony DI.
     *
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     *
     * @param ResponseHandler $responseHandler
     *
     * @return ControllerInterface|Controller|self
     */
    public function setResponseHandler(ResponseHandler $responseHandler);

    /**
     * Getter method for `ResponseHandler` service.
     *
     * @throws UnexpectedValueException
     *
     * @return ResponseHandlerInterface
     */
    public function getResponseHandler(): ResponseHandlerInterface;

    /**
     * Getter method for used DTO class for current controller.
     *
     * @param string|null $method
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getDtoClass(?string $method = null): string;

    /**
     * Method to validate REST trait method.
     *
     * @param Request  $request
     * @param string[] $allowedHttpMethods
     *
     * @throws LogicException
     * @throws MethodNotAllowedHttpException
     */
    public function validateRestMethod(Request $request, array $allowedHttpMethods): void;

    /**
     * Method to handle possible REST method trait exception.
     *
     * @param Throwable   $exception
     * @param string|null $id
     *
     * @throws Throwable
     *
     * @return Throwable
     */
    public function handleRestMethodException(Throwable $exception, ?string $id = null): Throwable;

    /**
     * Method to process current criteria array.
     *
     * @param array $criteria
     */
    public function processCriteria(array &$criteria): void;
}
