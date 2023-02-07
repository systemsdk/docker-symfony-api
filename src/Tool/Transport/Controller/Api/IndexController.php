<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class IndexController
 *
 * @package App\Tool
 */
#[AsController]
class IndexController
{
    /**
     * Default application response when requested root.
     *
     * @OA\Get(security={})
     *
     * @OA\Response(
     *      response=200,
     *      description="success",
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse();
    }
}
