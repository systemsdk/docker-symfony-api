<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\v1\User;

use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserGroupsController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\User
 */
class UserGroupsController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * Fetch specified user user groups, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="User groups",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=\App\User\Domain\Entity\UserGroup::class,
     *                 groups={"UserGroup", "UserGroup.role"},
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
     *  )
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
     */
    #[Route(
        path: '/v1/user/{requestUser}/groups',
        requirements: [
            'requestUser' => '%app.uuid_v1_regex%',
        ],
        methods: [Request::METHOD_GET],
    )]
    #[Security('is_granted("IS_USER_HIMSELF", requestUser) or is_granted("ROLE_ROOT")')]
    #[ParamConverter(
        data: 'requestUser',
        class: UserResource::class,
    )]
    public function __invoke(User $requestUser): JsonResponse
    {
        $groups = [
            'groups' => [
                UserGroup::SET_USER_GROUP_BASIC,
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($requestUser->getUserGroups()->getValues(), 'json', $groups),
            json: true
        );
    }
}
