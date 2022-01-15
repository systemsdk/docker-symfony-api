<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Localization;

use App\Service\LocalizationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LocaleController
 *
 * @OA\Get(security={})
 *
 * @OA\Tag(name="Localization")
 *
 * @package App\Controller\Api\v1\Localization
 */
class LocaleController
{
    public function __construct(
        private LocalizationService $localization,
    ) {
    }

    /**
     * Endpoint action to get supported locales. This is for use to choose what
     * locale your frontend application can use within its number, time, date,
     * datetime, etc. formatting.
     *
     * @OA\Response(
     *      response=200,
     *      description="List of locale strings.",
     *      @OA\JsonContent(
     *          type="array",
     *          example={"en","ru","fi"},
     *          @OA\Items(type="string"),
     *      ),
     *  )
     */
    #[Route(
        path: '/v1/localization/locale',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLocales());
    }
}
