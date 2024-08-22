<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestUpdateResourceInterface;
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
        ?array $allowedHttpMethods = null,
    ): Response {
        /** @var RestResourceInterface|RestUpdateResourceInterface $resource */
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_PUT]);

        try {
            $entityManagerName = RequestHandler::getTenant($request);
            $data = $resource->update(id: $id, dto: $restDto, flush: true, entityManagerName: $entityManagerName);

            return $this->getResponseHandler()->createResponse($request, $data, $resource); /** @phpstan-ignore-line */
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id, $entityManagerName ?? null);
        }
    }
}
