<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/AbstractGenericMethods.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\Interfaces\RestResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

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
     * {@inheritdoc}
     */
    abstract public function getResponseHandler(): ResponseHandlerInterface;

    /**
     * {@inheritdoc}
     */
    abstract public function processCriteria(array &$criteria, Request $request, string $method): void;

    /**
     * {@inheritdoc}
     */
    abstract public function handleRestMethodException(Throwable $exception, ?string $id = null): Throwable;
}
