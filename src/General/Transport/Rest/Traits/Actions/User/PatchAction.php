<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\User;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait PatchAction
 *
 * Trait to add 'patchAction' for REST controllers for 'ROLE_USER' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\PatchMethod for detailed documents.
 *
 * @package App\General
 */
trait PatchAction
{
    use PatchMethod;

    /**
     * Patch entity with new data, accessible only for 'ROLE_USER' users.
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
        methods: [Request::METHOD_PATCH],
    )]
    #[IsGranted(Role::USER->value)]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }
}
