<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/UpdateMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait UpdateMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait UpdateMethod
{
    /**
     * Generic 'updateMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function updateMethod(
        Request $request,
        RestDtoInterface $restDto,
        string $id,
        ?array $allowedHttpMethods = null
    ): Response {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? ['PUT']);

        try {
            $data = $resource->update($id, $restDto, true);

            return $this->getResponseHandler()->createResponse($request, $data, $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id);
        }
    }
}
