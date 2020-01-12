<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/VersionController.php
 */

namespace App\Controller\Api;

use App\Service\VersionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class VersionController
 *
 * @Route(
 *     path="/version",
 *  )
 *
 * @SWG\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class VersionController
{
    /**
     * Get API version.
     *
     * @Route(
     *     path="",
     *     methods={"GET"}
     *  )
     *
     * @SWG\Get(security={})
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
     *          type="object",
     *          example={"version": "1.0.0"},
     *          @SWG\Property(property="version", type="string", description="Version number"),
     *      ),
     *  )
     *
     * @param VersionService $version
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function index(VersionService $version): JsonResponse
    {
        $data = [
            'version' => $version->get(),
        ];

        return new JsonResponse($data);
    }
}
