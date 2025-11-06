<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\UserGroup;

use App\General\Transport\Rest\ResponseHandler;
use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * @package App\User
 */
#[AsController]
#[OA\Tag(name: 'UserGroup Management')]
class UsersController
{
    public function __construct(
        private readonly UserResource $userResource,
        private readonly ResponseHandler $responseHandler,
    ) {
    }

    /**
     * List specified user group users, accessible only for 'ROLE_ADMIN' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/v1/user_group/{userGroup}/users',
        requirements: [
            'userGroup' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::ADMIN->value)]
    #[OA\Response(
        response: 200,
        description: 'User group users',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: User::class,
                    groups: ['User', 'User.userGroups', 'User.roles', 'UserGroup', 'UserGroup.role']
                ),
            ),
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
    #[OA\Response(
        response: 404,
        description: 'User Group not found',
    )]
    public function __invoke(Request $request, UserGroup $userGroup): Response
    {
        return $this->responseHandler
            ->createResponse($request, $this->userResource->getUsersForGroup($userGroup), $this->userResource);
    }
}
