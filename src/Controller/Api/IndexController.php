<?php

declare(strict_types=1);

namespace App\Controller\Api;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class IndexController
 *
 * @package App\Controller\Api
 */
class IndexController
{
    /**
     * Default application response when requested root.
     *
     * @OA\Get(security={})
     *
     * @OA\Response(
     *      response=200,
     *      description="success",
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): Response
    {
        return new Response();
    }
}
