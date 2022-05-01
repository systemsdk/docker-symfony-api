<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait FindOneByMethod
 *
 * @package App\General
 *
 * @method ResponseHandler getResponseHandler()
 */
trait FindOneByMethod
{
    /**
     * Generic 'findOneByMethod' method for REST resources.
     *
     * @param array<int|string, string|array<mixed>> $criteria
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function findOneByMethod(Request $request, array $criteria, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_GET]);

        try {
            $orderBy = RequestHandler::getOrderBy($request);
            $entityManagerName = RequestHandler::getTenant($request);

            // Fetch data from database
            return $this
                ->getResponseHandler()
                ->createResponse(
                    $request,
                    $resource->findOneBy($criteria, $orderBy, true, $entityManagerName),
                    $resource
                );
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $criteria['id'] ?? null, $entityManagerName ?? null);
        }
    }
}
