<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Localization/LocaleController.php
 */

namespace App\Controller\Api\Localization;

use App\Service\LocalizationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LocaleController
 *
 * @Route(
 *     path="/localization/locale",
 *     methods={"GET"}
 *  )
 *
 * @OA\Get(security={})
 *
 * @OA\Tag(name="Localization")
 *
 * @package App\Controller\Api\Localization
 */
class LocaleController
{
    private LocalizationService $localization;

    /**
     * Constructor
     */
    public function __construct(LocalizationService $localization)
    {
        $this->localization = $localization;
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
     *          example={"en","ru"},
     *          @OA\Items(type="string"),
     *      ),
     *  )
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLocales());
    }
}
