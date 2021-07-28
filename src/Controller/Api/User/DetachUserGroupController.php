<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserResource;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class DetachUserGroupController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\Controller\Api\User
 */
class DetachUserGroupController
{
    public function __construct(
        private SerializerInterface $serializer,
        private UserResource $userResource,
    ) {
    }

    /**
     * Detach specified user group from specified user, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/user/{user}/group/{userGroup}",
     *      requirements={
     *          "user" = "%app.uuid_v1_regex%",
     *          "userGroup" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"DELETE"},
     *  )
     *
     * @ParamConverter(
     *      "user",
     *      class="App\Resource\UserResource",
     *  )
     * @ParamConverter(
     *      "userGroup",
     *      class="App\Resource\UserGroupResource",
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @OA\Parameter(
     *      name="userId",
     *      in="path",
     *      required=true,
     *      description="User GUID",
     *      @OA\Schema(
     *          type="string",
     *          default="User GUID",
     *      ),
     *  )
     * @OA\Parameter(
     *      name="userGroupId",
     *      in="path",
     *      required=true,
     *      description="User Group GUID",
     *      @OA\Schema(
     *          type="string",
     *          default="User Group GUID",
     *      ),
     *  )
     * @OA\Response(
     *      response=200,
     *      description="User groups",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              ref=@Model(
     *                  type=\App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @OA\Response(
     *      response=401,
     *      description="Invalid token (not found or expired)",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 401, "message": "JWT Token not found"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @OA\Response(
     *      response=403,
     *      description="Forbidden",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 403, "message": "Access denied"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(User $user, UserGroup $userGroup): JsonResponse
    {
        $this->userResource->save($user->removeUserGroup($userGroup));
        $groups = [
            'groups' => [
                UserGroup::SET_USER_GROUP_BASIC,
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($user->getUserGroups()->getValues(), 'json', $groups),
            json: true
        );
    }
}
