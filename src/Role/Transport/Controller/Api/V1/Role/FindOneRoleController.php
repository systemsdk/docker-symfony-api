<?php

declare(strict_types=1);

namespace App\Role\Transport\Controller\Api\V1\Role;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Methods;
use App\Role\Application\Resource\RoleResource;
use App\Role\Domain\Entity\Role as RoleEntity;
use App\Role\Domain\Enum\Role;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * @package App\Role
 */
#[AsController]
#[OA\Tag(name: 'Role Management')]
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
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            ref: new Model(type: RoleEntity::class, groups: ['Role']),
            type: 'object',
            example: [
                'id' => 'ROLE_ROOT',
                'description' => 'role root description',
            ],
        ),
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', description: 'Error code', type: 'integer'),
                new Property(property: 'message', description: 'Error description', type: 'string'),
            ],
            type: 'object',
            example: [
                'code' => 403,
                'message' => 'Access denied',
            ],
        ),
    )]
    public function __invoke(Request $request, string $role): Response
    {
        return $this->findOneMethod($request, $role);
    }
}
