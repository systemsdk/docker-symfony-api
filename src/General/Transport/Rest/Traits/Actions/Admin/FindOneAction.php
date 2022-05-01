<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Admin;

use App\General\Transport\Rest\Traits\Methods\FindOneMethod;
use App\Role\Domain\Entity\Role;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\General
 */
trait FindOneAction
{
    use FindOneMethod;

    /**
     * Find entity, accessible only for 'ROLE_ADMIN' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
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
            'id' => '%app.uuid_v1_regex%',
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
