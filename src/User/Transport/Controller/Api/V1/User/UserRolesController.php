<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\User;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\User
 */
#[AsController]
#[OA\Tag(name: 'User Management')]
class UserRolesController
{
    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Fetch specified user roles, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     */
    #[Route(
        path: '/v1/user/{user}/roles',
        requirements: [
            'user' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(new Expression('is_granted("IS_USER_HIMSELF", object) or "ROLE_ROOT" in role_names'), 'user')]
    #[OA\Response(
        response: 200,
        description: 'Specified user roles',
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
    public function __invoke(User $user): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles($user->getRoles()));
    }
}
