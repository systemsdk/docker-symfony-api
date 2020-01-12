<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/DefaultController.php
 */

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Class DefaultController
 *
 * @Route(
 *     path="/",
 *  )
 *
 * @package App\Controller\Api
 */
class DefaultController
{
    /**
     * Default application response when requested root.
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
     * )
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function index(): Response
    {
        return new Response('', Response::HTTP_OK);
    }
}
