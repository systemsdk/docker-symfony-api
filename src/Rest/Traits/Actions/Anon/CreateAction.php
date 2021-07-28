<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Anon;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\CreateMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait CreateAction
{
    use CreateMethod;

    /**
     * Create entity, accessible for anonymous users.
     *
     * @Route(
     *     path="",
     *     methods={"POST"},
     *  )
     *
     * @OA\RequestBody(
     *      request="body",
     *      description="object",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"param": "value"},
     *      )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="created",
     *     @OA\JsonContent(
     *         type="object",
     *         example={},
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
