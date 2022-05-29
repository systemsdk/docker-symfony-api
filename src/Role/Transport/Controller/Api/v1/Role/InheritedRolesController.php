<?php

declare(strict_types=1);

namespace App\Role\Transport\Controller\Api\v1\Role;

use App\Role\Application\Resource\RoleResource;
use App\Role\Application\Security\RolesService;
use App\Role\Domain\Entity\Role;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InheritedRolesController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Role
 */
class InheritedRolesController
{
    public function __construct(
        private readonly RolesService $rolesService,
    ) {
    }

    /**
     * Return all inherited roles as an array for specified Role, accessible for 'ROLE_ADMIN' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="Inherited roles",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
     *     ),
     * )
     * @OA\Response(
     *     response=401,
     *     description="Invalid token (not found or expired)",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 401, "message": "JWT Token not found"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     */
    #[Route(
        path: '/v1/role/{role}/inherited',
        requirements: [
            'role' => '^ROLE_\w+$',
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::ROLE_ADMIN)]
    #[ParamConverter(
        data: 'role',
        class: RoleResource::class,
    )]
    public function __invoke(Role $role): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles([$role->getId()]));
    }
}
