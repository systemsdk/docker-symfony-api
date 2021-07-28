<?php

declare(strict_types=1);

namespace App\Rest\Traits\Methods;

use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait DeleteMethod
 *
 * @package App\Rest\Traits\Methods
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
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_DELETE]);

        try {
            // Fetch data from database
            return $this
                ->getResponseHandler()
                ->createResponse($request, $resource->delete($id), $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id);
        }
    }
}
