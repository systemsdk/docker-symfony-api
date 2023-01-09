<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

/**
 * Trait DeleteAction
 *
 * Trait to add 'deleteAction' for REST controllers for anonymous users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\General
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
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_DELETE],
    )]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
