<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/DeleteMethod.php
 */

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
    // Traits
    use AbstractGenericMethods;

    /**
     * Generic 'deleteMethod' method for REST resources.
     *
     * @param Request     $request
     * @param string      $id
     * @param array|null  $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function deleteMethod(Request $request, string $id, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->validateRestMethodAndGetResource($request, $allowedHttpMethods ?? ['DELETE']);

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
