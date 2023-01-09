<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Logged;

use App\General\Transport\Rest\Traits\Methods\CountMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait CountAction
 *
 * Trait to add 'countAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\General
 */
trait CountAction
{
    use CountMethod;

    /**
     * Count entities, accessible only for 'ROLE_LOGGED' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"count": "1"},
     *         @OA\Property(property="count", type="integer"),
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
        path: '/count',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::LOGGED->value)]
    public function countAction(Request $request): Response
    {
        return $this->countMethod($request);
    }
}
