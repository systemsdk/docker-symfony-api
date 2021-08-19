<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Anon;

use App\Rest\Traits\Methods\DeleteMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait DeleteAction
 *
 * Trait to add 'deleteAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait DeleteAction
{
    use DeleteMethod;

    /**
     * Delete entity, accessible for anonymous users.
     *
     * @OA\Response(
     *     response=200,
     *     description="deleted",
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
        methods: [Request::METHOD_DELETE],
    )]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
