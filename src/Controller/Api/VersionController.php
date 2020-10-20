<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/VersionController.php
 */

namespace App\Controller\Api;

use App\Service\VersionService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class VersionController
 *
 * @OA\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class VersionController
{
    private VersionService $versionService;

    /**
     * Constructor
     */
    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    /**
     * Get API version.
     *
     * @Route(
     *     path="/version",
     *     methods={"GET"}
     *  )
     *
     * @OA\Get(security={})
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\Schema(
     *         type="object",
     *         example={"version": "1.0.0"},
     *         @OA\Property(property="version", type="string", description="Version number"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['version' => $this->versionService->get()]);
    }
}
