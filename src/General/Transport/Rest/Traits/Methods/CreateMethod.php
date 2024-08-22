<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\Interfaces\RestCreateResourceInterface;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
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
        ?array $allowedHttpMethods = null,
    ): Response {
        /** @var RestResourceInterface|RestCreateResourceInterface $resource */
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_POST]);

        try {
            $entityManagerName = RequestHandler::getTenant($request);
            $data = $resource->create(dto: $restDto, flush: true, entityManagerName: $entityManagerName);

            return $this
                ->getResponseHandler()
                ->createResponse($request, $data, $resource, Response::HTTP_CREATED); /** @phpstan-ignore-line */
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException(
                exception: $exception,
                entityManagerName: $entityManagerName ?? null
            );
        }
    }
}
