<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Logged;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\PatchMethod;
use App\Security\RolesService;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait PatchAction
 *
 * Trait to add 'patchAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\Rest\Traits\Methods\PatchMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Root
 */
trait PatchAction
{
    use PatchMethod;

    /**
     * Patch entity with new data, accessible only for 'ROLE_LOGGED' users.
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
     *      response=403,
     *      description="Access denied",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 403, "message": "Access denied"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => '%app.uuid_v1_regex%',
        ],
        methods: [Request::METHOD_PATCH],
    )]
    #[IsGranted(RolesService::ROLE_LOGGED)]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }
}
