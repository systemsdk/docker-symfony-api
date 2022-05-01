<?php

declare(strict_types=1);

namespace App\Tool\Transport\Controller\Api\v1\Localization;

use App\Tool\Application\Service\LocalizationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LanguageController
 *
 * @OA\Get(security={})
 *
 * @OA\Tag(name="Localization")
 *
 * @package App\Tool
 */
class LanguageController
{
    public function __construct(
        private LocalizationService $localization,
    ) {
    }

    /**
     * Endpoint action to get supported languages. This is for use to choose
     * what language your frontend application can use within its translations.
     *
     * @OA\Response(
     *      response=200,
     *      description="List of language strings.",
     *      @OA\JsonContent(
     *          type="array",
     *          example={"en","ru","fi"},
     *          @OA\Items(type="string"),
     *      ),
     *  )
     */
    #[Route(
        path: '/v1/localization/language',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLanguages());
    }
}
