<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/PatchMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait PatchMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait PatchMethod
{
    // Traits
    use AbstractGenericMethods;

    /**
     * Generic 'patchMethod' method for REST resources.
     *
     * @param Request          $request
     * @param RestDtoInterface $restDto
     * @param string           $id
     * @param array|null       $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function patchMethod(
        Request $request,
        RestDtoInterface $restDto,
        string $id,
        ?array $allowedHttpMethods = null
    ): Response {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? ['PATCH']);

        try {
            $data = $resource->patch($id, $restDto, true);

            return $this->getResponseHandler()->createResponse($request, $data, $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id);
        }
    }
}
