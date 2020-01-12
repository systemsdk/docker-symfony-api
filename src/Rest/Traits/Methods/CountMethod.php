<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/CountMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\RequestHandler;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait CountMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait CountMethod
{
    // Traits
    use AbstractGenericMethods;

    /**
     * Generic 'countMethod' method for REST resources.
     *
     * @param Request       $request
     * @param array|null $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function countMethod(Request $request, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->validateRestMethodAndGetResource($request, $allowedHttpMethods ?? ['GET']);
        // Determine used parameters
        $search = RequestHandler::getSearchTerms($request);

        try {
            $criteria = RequestHandler::getCriteria($request);
            $this->processCriteria($criteria);

            return $this
                ->getResponseHandler()
                ->createResponse($request, ['count' => $resource->count($criteria, $search)], $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception);
        }
    }
}
