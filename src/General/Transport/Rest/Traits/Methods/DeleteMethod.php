<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\Rest\Interfaces\RestDeleteResourceInterface;
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
trait DeleteMethod
{
    /**
     * Generic 'deleteMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function deleteMethod(Request $request, string $id, ?array $allowedHttpMethods = null): Response
    {
        /** @var RestResourceInterface|RestDeleteResourceInterface $resource */
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_DELETE]);

        try {
            $entityManagerName = RequestHandler::getTenant($request);

            // Fetch data from database
            return $this
                ->getResponseHandler()
                ->createResponse(
                    $request,
                    $resource->delete(id: $id, entityManagerName: $entityManagerName), /** @phpstan-ignore-next-line */
                    $resource
                );
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id, $entityManagerName ?? null);
        }
    }
}
