<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Root;

use App\Rest\Traits\Methods\IdsMethod;
use App\Security\RolesService;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait IdsAction
 *
 * Trait to add 'idsAction' for REST controllers for 'ROLE_ROOT' users.
 *
 * @see \App\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Root
 */
trait IdsAction
{
    use IdsMethod;

    /**
     * Find ids list, accessible only for 'ROLE_ROOT' users.
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
    #[IsGranted(RolesService::ROLE_ROOT)]
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
