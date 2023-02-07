<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api;

use App\General\Transport\Rest\Interfaces\ResponseHandlerInterface;
use App\General\Transport\Rest\ResponseHandler;
use App\Tool\Application\Service\HealthService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class HealthController
 *
 * @OA\Tag(name="Tools")
 *
 * @package App\Tool
 */
#[AsController]
class HealthController
{
    public function __construct(
        private readonly ResponseHandler $responseHandler,
        private readonly HealthService $healthService,
    ) {
    }

    /**
     * Route for application health check. This action will make some simple tasks to ensure that application is up
     * and running like expected.
     *
     * @see https://kubernetes.io/docs/tasks/configure-pod-container/configure-liveness-readiness-probes/
     *
     * @OA\Get(security={})
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"timestamp": "2019-08-01T09:00:00+00:00"},
     *         @OA\Property(property="timestamp", type="string"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/health',
        methods: [Request::METHOD_GET],
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
