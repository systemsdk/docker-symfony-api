<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\VersionService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class VersionController
 *
 * @OA\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class VersionController
{
    public function __construct(
        private VersionService $versionService,
    ) {
    }

    /**
     * Get API version.
     *
     * @OA\Get(security={})
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"version": "1.0.0"},
     *         @OA\Property(property="version", type="string", description="Version number"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/version',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['version' => $this->versionService->get()]);
    }
}
