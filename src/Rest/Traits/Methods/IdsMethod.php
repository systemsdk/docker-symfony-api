<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/FindOneMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\RequestHandler;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait IdsMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait IdsMethod
{
    // Traits
    use AbstractGenericMethods;

    /**
     * Generic 'idsMethod' method for REST resources.
     *
     * @param Request     $request
     * @param array|null  $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function idsMethod(Request $request, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? ['GET']);
        // Determine used parameters
        $search = RequestHandler::getSearchTerms($request);

        try {
            $criteria = RequestHandler::getCriteria($request);
            $this->processCriteria($criteria);

            return $this
                ->getResponseHandler()
                ->createResponse($request, $resource->getIds($criteria, $search), $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception);
        }
    }
}
