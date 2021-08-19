<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Rest\ResponseHandler;
use App\Service\HealthService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class HealthController
 *
 * @OA\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class HealthController
{
    public function __construct(
        private ResponseHandler $responseHandler,
        private HealthService $healthService,
    ) {
    }

    /**
     * Some simple tasks to ensure that application is up and running like expected.
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
            format: ResponseHandler::FORMAT_JSON,
            context: [
                'groups' => [
                    'Health.timestamp',
                ],
            ],
        );
    }
}
