<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Root;

use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait DeleteAction
 *
 * Trait to add 'deleteAction' for REST controllers for 'ROLE_ROOT' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\General
 */
trait DeleteAction
{
    use DeleteMethod;

    /**
     * Delete entity, accessible only for 'ROLE_ROOT' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="deleted",
     *     @OA\JsonContent(
     *         type="object",
     *         example={},
     *     ),
     * )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 403, "message": "Access denied"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
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
    #[IsGranted(Role::ROOT->value)]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
