<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/FindOneMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait FindOneMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait FindOneMethod
{
    // Traits
    use AbstractGenericMethods;

    /**
     * Generic 'findOneMethod' method for REST resources.
     *
     * @param Request     $request
     * @param string      $id
     * @param array|null  $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function findOneMethod(Request $request, string $id, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->validateRestMethodAndGetResource($request, $allowedHttpMethods ?? ['GET']);

        try {
            // Fetch data from database
            return $this
                ->getResponseHandler()
                ->createResponse($request, $resource->findOne($id, true), $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id);
        }
    }
}
