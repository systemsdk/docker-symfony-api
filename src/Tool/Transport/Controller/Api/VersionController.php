<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api;

use App\Tool\Application\Service\Interfaces\VersionServiceInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * @package App\Tool
 */
#[AsController]
#[OA\Tag(name: 'Tools')]
class VersionController
{
    public function __construct(
        private readonly VersionServiceInterface $versionService,
    ) {
    }

    /**
     * Get API version.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/version',
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
                            property: 'version',
                            description: 'REST API version',
                            type: 'string',
                        ),
                    ],
                    type: 'object',
                    example: [
                        'version' => '1.0.0',
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->versionService->get(),
        ]);
    }
}
