<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Localization/TimezoneController.php
 */

namespace App\Controller\Api\Localization;

use App\Service\LocalizationService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class TimezoneController
 *
 * @Route(
 *     path="/localization/timezone",
 *     methods={"GET"}
 *  )
 *
 * @SWG\Get(security={})
 *
 * @SWG\Tag(name="Localization")
 *
 * @package App\Controller\Api\Localization
 */
class TimeZoneController
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
     * Endpoint action to get list of supported timezones. This is for use to
     * choose what timezone your frontend application can use within its date,
     * time,  datetime, etc. formatting.
     *
     * @SWG\Response(
     *      response=200,
     *      description="List of timezone objects.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="timezone",
     *                  type="string",
     *                  example="Europe",
     *                  description="Africa,America,Antarctica,Arctic,Asia,Atlantic,Australia,Europe,Pacific,UTC."
     *              ),
     *              @SWG\Property(
     *                  property="identier",
     *                  type="string",
     *                  example="Europe/Kiev",
     *                  description="Timezone identifier that you can use with other librariers."
     *              ),
     *              @SWG\Property(
     *                  property="offset",
     *                  type="string",
     *                  example="GMT+2:00",
     *                  description="GMT offset of identifier."
     *              ),
     *              @SWG\Property(
     *                  property="value",
     *                  type="string",
     *                  example="Europe/Kiev",
     *                  description="User friendly value of identifier value eg. '_' characters are replaced by space."
     *              ),
     *          ),
     *      ),
     *  )
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getTimezones());
    }
}
