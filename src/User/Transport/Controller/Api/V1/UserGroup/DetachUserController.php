<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\UserGroup;

use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * @package App\User
 */
#[AsController]
#[OA\Tag(name: 'UserGroup Management')]
class DetachUserController
{
    public function __construct(
        private readonly UserResource $userResource,
        private readonly UserGroupResource $userGroupResource,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * Detach specified user from specified user group, accessible only for 'ROLE_ROOT' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/v1/user_group/{userGroup}/user/{user}',
        requirements: [
            'userGroup' => Requirement::UUID_V1,
            'user' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_DELETE],
    )]
    #[IsGranted(Role::ROOT->value)]
    #[OA\Parameter(name: 'userGroup', description: 'User Group GUID', in: 'path', required: true)]
    #[OA\Parameter(name: 'user', description: 'User GUID', in: 'path', required: true)]
    #[OA\Response(
        response: 200,
        description: 'List of users in this user group',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: User::class, groups: ['User']),
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
    public function __invoke(UserGroup $userGroup, User $user): JsonResponse
    {
        $this->userGroupResource->save($userGroup->removeUser($user), false);
        $this->userResource->save($user, true, true);
        $groups = [
            'groups' => [
                User::SET_USER_BASIC,
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($userGroup->getUsers()->getValues(), 'json', $groups),
            json: true,
        );
    }
}
