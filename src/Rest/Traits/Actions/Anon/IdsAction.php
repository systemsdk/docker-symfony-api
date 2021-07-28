<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Anon;

use App\Rest\Traits\Methods\IdsMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait IdsAction
 *
 * Trait to add 'idsAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait IdsAction
{
    use IdsMethod;

    /**
     * Find ids list, accessible for anonymous users.
     *
     * @Route(
     *     path="/ids",
     *     methods={"GET"},
     *  )
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
