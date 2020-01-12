<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/HealthController.php
 */

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Rest\ResponseHandler;
use App\Service\HealthService;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class HealthController
 *
 * @Route(
 *     path="/health",
 *  )
 *
 * @SWG\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class HealthController
{
    /**
     * Some simple tasks to ensure that application is up and running like expected.
     *
     * @link https://kubernetes.io/docs/tasks/configure-pod-container/configure-liveness-readiness-probes/
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
     *          example={"timestamp": "2019-08-01T09:00:00+00:00"},
     *          @SWG\Property(property="timestamp", type="string"),
     *      ),
     *  )
     *
     * @param Request         $request
     * @param ResponseHandler $responseHandler
     * @param HealthService   $healthService
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function index(Request $request, ResponseHandler $responseHandler, HealthService $healthService): Response
    {
        return $responseHandler->createResponse(
            $request,
            $healthService->check(),
            null,
            Response::HTTP_OK,
            ResponseHandler::FORMAT_JSON,
            [
                'groups' => [
                    'Health.timestamp',
                ],
            ]
        );
    }
}
