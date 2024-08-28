<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * @package App\Tool
 */
#[AsController]
class IndexController
{
    /**
     * Default application response when requested root.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'success',
            ),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse();
    }
}
