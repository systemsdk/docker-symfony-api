<?php

declare(strict_types=1);

namespace App\Role\Transport\Controller\Api\v1\Role;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Methods;
use App\Role\Application\Resource\RoleResource;
use App\Role\Domain\Entity\Role as RoleEntity;
use App\Role\Domain\Enum\Role;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Class FindOneRoleController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Role
 */
class FindOneRoleController extends Controller
{
    use Methods\FindOneMethod;

    public function __construct(
        RoleResource $resource,
    ) {
        parent::__construct($resource);
    }

    /**
     * Find role entity, accessible for 'ROLE_ADMIN' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         example={"id": "ROLE_ROOT", "description": "role root description"},
     *         ref=@Model(
     *             type=RoleEntity::class,
     *             groups={"Role"},
     *         ),
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
     *  )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/v1/role/{role}',
        requirements: [
            'role' => new EnumRequirement(Role::class),
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::ADMIN->value)]
    public function __invoke(Request $request, string $role): Response
    {
        return $this->findOneMethod($request, $role);
    }
}
