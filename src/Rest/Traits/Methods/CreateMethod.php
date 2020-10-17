<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/CreateMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait CreateMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait CreateMethod
{
    /**
     * Generic 'createMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function createMethod(
        Request $request,
        RestDtoInterface $restDto,
        ?array $allowedHttpMethods = null
    ): Response {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? ['POST']);

        try {
            $data = $resource->create($restDto, true);

            return $this
                ->getResponseHandler()
                ->createResponse($request, $data, $resource, Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception);
        }
    }
}
