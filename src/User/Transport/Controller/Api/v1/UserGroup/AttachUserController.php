<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\v1\UserGroup;

use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class AttachUserController
 *
 * @OA\Tag(name="UserGroup Management")
 *
 * @package App\User
 */
class AttachUserController
{
    public function __construct(
        private readonly UserResource $userResource,
        private readonly UserGroupResource $userGroupResource,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * Attach specified user to specified user group, accessible only for 'ROLE_ROOT' users.
     *
     * @OA\Parameter(
     *     name="userGroupId",
     *     in="path",
     *     required=true,
     *     description="User Group GUID",
     *     @OA\Schema(
     *         type="string",
     *         default="User Group GUID",
     *     )
     * )
     * @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     description="User GUID",
     *     @OA\Schema(
     *         type="string",
     *         default="User GUID",
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="List of user group users - specified user already exists on this group",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=\App\User\Domain\Entity\User::class,
     *                 groups={"User"},
     *             ),
     *         ),
     *     ),
     * )
     * @OA\Response(
     *     response=201,
     *     description="List of user group users - specified user has been attached to this group",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=\App\User\Domain\Entity\User::class,
     *                 groups={"User"},
     *             ),
     *         ),
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
        path: '/v1/user_group/{userGroup}/user/{user}',
        requirements: [
            'userGroup' => Requirement::UUID_V1,
            'user' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(Role::ROOT->value)]
    public function __invoke(UserGroup $userGroup, User $user): JsonResponse
    {
        $status = $userGroup->getUsers()->contains($user) ? Response::HTTP_OK : Response::HTTP_CREATED;
        $this->userGroupResource->save($userGroup->addUser($user), false);
        $this->userResource->save($user, true, true);
        $groups = [
            'groups' => [
                User::SET_USER_BASIC,
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($userGroup->getUsers()->getValues(), 'json', $groups),
            $status,
            json: true,
        );
    }
}
