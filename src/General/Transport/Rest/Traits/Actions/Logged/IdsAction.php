<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Logged;

use App\General\Transport\Rest\Traits\Methods\IdsMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait IdsAction
 *
 * Trait to add 'idsAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\General
 */
trait IdsAction
{
    use IdsMethod;

    /**
     * Find ids list, accessible only for 'ROLE_LOGGED' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
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
        path: '/ids',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::LOGGED->value)]
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
