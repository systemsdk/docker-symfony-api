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
use Throwable;

/**
 * @package App\Tool
 */
#[AsController]
#[OA\Tag(name: 'Localization')]
class TimeZoneController
{
    public function __construct(
        private readonly LocalizationServiceInterface $localization,
    ) {
    }

    /**
     * Endpoint action to get list of supported timezones. This is for use to
     * choose what timezone your frontend application can use within its date,
     * time, datetime, etc. formatting.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/v1/localization/timezone',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of timezone objects.',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'timezone',
                                description: 'Africa,America,Antarctica,Asia,Atlantic,Australia,Europe,Pacific,UTC.',
                                type: 'string',
                                example: 'Europe',
                            ),
                            new OA\Property(
                                property: 'identifier',
                                description: 'Timezone identifier that you can use with other libraries.',
                                type: 'string',
                                example: 'Europe/Kyiv',
                            ),
                            new OA\Property(
                                property: 'offset',
                                description: 'GMT offset of identifier.',
                                type: 'string',
                                example: 'GMT+2:00',
                            ),
                            new OA\Property(
                                property: 'value',
                                description: 'User friendly identifier value (underscores replaced with spaces).',
                                type: 'string',
                                example: 'Europe/Kyiv',
                            ),
                        ],
                        type: 'object'
                    ),
                    example: [
                        'timezone' => 'Europe',
                        'identifier' => 'Europe/Kyiv',
                        'offset' => 'GMT+2:00',
                        'value' => 'Europe/Kyiv',
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getTimezones());
    }
}
