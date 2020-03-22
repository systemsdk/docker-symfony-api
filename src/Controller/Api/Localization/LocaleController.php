<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Localization/LocaleController.php
 */

namespace App\Controller\Api\Localization;

use App\Service\LocalizationService;
use Swagger\Annotations as SWG;
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
 * @SWG\Get(security={})
 *
 * @SWG\Tag(name="Localization")
 *
 * @package App\Controller\Api\Localization
 */
class LocaleController
{
    private LocalizationService $localization;

    /**
     * Constructor
     *
     * @param LocalizationService $localization
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
     * @SWG\Response(
     *      response=200,
     *      description="List of locale strings.",
     *      @SWG\Schema(
     *          type="array",
     *          example={"en","ru"},
     *          @SWG\Items(type="string"),
     *      ),
     *  )
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLocales());
    }
}
