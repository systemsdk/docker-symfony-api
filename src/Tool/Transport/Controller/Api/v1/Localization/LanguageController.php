<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api\v1\Localization;

use App\Tool\Application\Service\LocalizationService;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Tool
 */
#[AsController]
#[OA\Tag(name: 'Localization')]
class LanguageController
{
    public function __construct(
        private readonly LocalizationService $localization,
    ) {
    }

    /**
     * Endpoint action to get supported languages. This is for use to choose
     * what language your frontend application can use within its translations.
     */
    #[Route(
        path: '/v1/localization/language',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of language strings.',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                        example: 'en',
                    ),
                    example: ['en', 'ru', 'fi'],
                ),
            ),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLanguages());
    }
}
