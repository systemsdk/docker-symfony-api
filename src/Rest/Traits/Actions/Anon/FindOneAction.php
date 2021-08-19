<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Anon;

use App\Rest\Traits\Methods\FindOneMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait FindOneAction
{
    use FindOneMethod;

    /**
     * Find entity, accessible for anonymous users.
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
            'id' => '%app.uuid_v1_regex%',
        ],
        methods: [Request::METHOD_GET],
    )]
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
