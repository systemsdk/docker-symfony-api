<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api;

use App\General\Transport\Rest\Interfaces\ResponseHandlerInterface;
use App\General\Transport\Rest\ResponseHandler;
use App\Tool\Application\Service\Interfaces\HealthServiceInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * @package App\Tool
 */
#[AsController]
#[OA\Tag(name: 'Tools')]
class HealthController
{
    public function __construct(
        private readonly ResponseHandler $responseHandler,
        private readonly HealthServiceInterface $healthService,
    ) {
    }

    /**
     * Route for application health check. This action will make some simple tasks to ensure that application is up
     * and running like expected.
     *
     * @see https://kubernetes.io/docs/tasks/configure-pod-container/configure-liveness-readiness-probes/
     *
     * @throws Throwable
     */
    #[Route(
        path: '/health',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'success',
                content: new JsonContent(
                    properties: [
                        new Property(
                            property: 'timestamp',
                            description: 'Timestamp when health check was performed',
                            type: 'string',
                        ),
                    ],
                    type: 'object',
                    example: [
                        'timestamp' => '2019-08-01T09:00:00+00:00',
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(Request $request): Response
    {
        return $this->responseHandler->createResponse(
            $request,
            $this->healthService->check(),
            format: ResponseHandlerInterface::FORMAT_JSON,
            context: [
                'groups' => [
                    'Health.timestamp',
                ],
            ],
        );
    }
}
