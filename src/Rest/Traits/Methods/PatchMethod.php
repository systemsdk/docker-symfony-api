<?php

declare(strict_types=1);

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
    /**
     * Generic 'patchMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function patchMethod(
        Request $request,
        RestDtoInterface $restDto,
        string $id,
        ?array $allowedHttpMethods = null,
    ): Response {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_PATCH]);

        try {
            $data = $resource->patch($id, $restDto, true);

            return $this->getResponseHandler()->createResponse($request, $data, $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id);
        }
    }
}
