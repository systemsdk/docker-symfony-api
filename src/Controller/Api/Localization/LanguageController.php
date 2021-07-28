<?php

declare(strict_types=1);

namespace App\Controller\Api\Localization;

use App\Service\LocalizationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LanguageController
 *
 * @Route(
 *     path="/localization/language",
 *     methods={"GET"}
 *  )
 *
 * @OA\Get(security={})
 *
 * @OA\Tag(name="Localization")
 *
 * @package App\Controller\Api\Localization
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
     *          example={"en","ru"},
     *          @OA\Items(type="string"),
     *      ),
     *  )
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLanguages());
    }
}
