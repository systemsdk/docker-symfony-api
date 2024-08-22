<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use App\General\Transport\Rest\ResponseHandler;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use UnexpectedValueException;

/**
 * @package App\General
 */
#[AutoconfigureTag('app.rest.controller')]
interface ControllerInterface
{
    /**
     * Getter method for `resource` service.
     *
     * @throws UnexpectedValueException
     */
    public function getResource(): RestResourceInterface|RestSmallResourceInterface;

    /**
     * Getter method for `ResponseHandler` service.
     *
     * @throws UnexpectedValueException
     */
    public function getResponseHandler(): ResponseHandlerInterface;

    /**
     * Setter method for `ResponseHandler` service, this is called by Symfony DI.
     */
    public function setResponseHandler(ResponseHandler $responseHandler): static;

    /**
     * Getter method for used DTO class for current controller.
     *
     * @throws UnexpectedValueException
     */
    public function getDtoClass(?string $method = null): string;

    /**
     * Method to validate REST trait method.
     *
     * @param array<int, string> $allowedHttpMethods
     *
     * @throws LogicException
     * @throws MethodNotAllowedHttpException
     */
    public function validateRestMethod(Request $request, array $allowedHttpMethods): void;

    /**
     * Method to handle possible REST method trait exception.
     */
    public function handleRestMethodException(
        Throwable $exception,
        ?string $id = null,
        ?string $entityManagerName = null
    ): Throwable;

    /**
     * @param array<int, string> $allowedHttpMethods
     */
    public function getResourceForMethod(
        Request $request,
        array $allowedHttpMethods
    ): RestResourceInterface|RestSmallResourceInterface;

    /**
     * Method to process current criteria array.
     *
     * @param array<int|string, string|array<mixed>> $criteria
     */
    public function processCriteria(array &$criteria, Request $request, string $method): void;
}
