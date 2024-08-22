<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\Rest\Interfaces\RestIdsResourceInterface;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\General
 *
 * @method ResponseHandler getResponseHandler()
 */
trait IdsMethod
{
    /**
     * Generic 'idsMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function idsMethod(Request $request, ?array $allowedHttpMethods = null): Response
    {
        /** @var RestResourceInterface|RestIdsResourceInterface $resource */
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_GET]);
        // Determine used parameters
        $search = RequestHandler::getSearchTerms($request);

        try {
            $criteria = RequestHandler::getCriteria($request);
            $entityManagerName = RequestHandler::getTenant($request);
            $this->processCriteria($criteria, $request, __METHOD__);

            return $this
                ->getResponseHandler()
                ->createResponse(
                    $request,
                    $resource->getIds($criteria, $search, $entityManagerName), /** @phpstan-ignore-next-line */
                    $resource
                );
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException(
                exception: $exception,
                entityManagerName: $entityManagerName ?? null
            );
        }
    }
}
