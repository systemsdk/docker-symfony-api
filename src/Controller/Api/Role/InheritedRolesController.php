<?php

declare(strict_types=1);

namespace App\Controller\Api\Role;

use App\Entity\Role;
use App\Resource\RoleResource;
use App\Security\RolesService;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class InheritedRolesController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Controller\Api\Role
 */
class InheritedRolesController
{
    public function __construct(
        private RolesService $rolesService,
    ) {
    }

    /**
     * Return all inherited roles as an array for specified Role, accessible for 'IS_AUTHENTICATED_FULLY' users.
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
        path: '/role/{role}/inherited',
        requirements: ['role' => '^ROLE_\w+$'],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[ParamConverter(
        data: 'role',
        class: RoleResource::class,
    )]
    public function __invoke(Role $role): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles([$role->getId()]));
    }
}
