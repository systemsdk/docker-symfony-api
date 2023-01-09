<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

/**
 * Trait UpdateAction
 *
 * Trait to add 'updateAction' for REST controllers for anonymous users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\UpdateMethod for detailed documents.
 *
 * @package App\General
 */
trait UpdateAction
{
    use UpdateMethod;

    /**
     * Update entity with new data, accessible for anonymous users.
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
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={},
     *     ),
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_PUT],
    )]
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }
}
