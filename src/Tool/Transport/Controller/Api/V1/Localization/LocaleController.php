<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api\V1\Localization;

use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @package App\Tool
 */
#[AsController]
#[OA\Tag(name: 'Localization')]
class LocaleController
{
    public function __construct(
        private readonly LocalizationServiceInterface $localization,
    ) {
    }

    /**
     * Endpoint action to get supported locales. This is for use to choose what
     * locale your frontend application can use within its number, time, date,
     * datetime, etc. formatting.
     */
    #[Route(
        path: '/v1/localization/locale',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of locale strings.',
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
        return new JsonResponse($this->localization->getLocales());
    }
}
