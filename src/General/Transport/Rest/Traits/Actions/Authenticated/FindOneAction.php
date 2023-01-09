<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Authenticated;

use App\General\Transport\Rest\Traits\Methods\FindOneMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for authenticated users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\General
 */
trait FindOneAction
{
    use FindOneMethod;

    /**
     * Find entity, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          type="object",
     *          example={},
     *      ),
     *  )
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
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
