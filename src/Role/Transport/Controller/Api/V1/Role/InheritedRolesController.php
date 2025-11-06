<?php

declare(strict_types=1);

namespace App\Role\Transport\Controller\Api\V1\Role;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Domain\Entity\Role;
use App\Role\Domain\Enum\Role as RoleEnum;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Role
 */
#[AsController]
#[OA\Tag(name: 'Role Management')]
class InheritedRolesController
{
    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Return all inherited roles as an array for specified Role, accessible for 'ROLE_ADMIN' users.
     */
    #[Route(
        path: '/v1/role/{role}/inherited',
        requirements: [
            'role' => new EnumRequirement(RoleEnum::class),
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(RoleEnum::ADMIN->value)]
    #[OA\Response(
        response: 200,
        description: 'Inherited roles',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(type: 'string', example: 'ROLE_USER'),
            example: ['ROLE_USER', 'ROLE_LOGGED'],
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid token (not found or expired)',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', description: 'Error code', type: 'integer'),
                new Property(property: 'message', description: 'Error description', type: 'string'),
            ],
            type: 'object',
            example: [
                'code' => 401,
                'message' => 'JWT Token not found',
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
    public function __invoke(Role $role): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles([$role->getId()]));
    }
}
