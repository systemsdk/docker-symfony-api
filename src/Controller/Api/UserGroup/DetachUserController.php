<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/UserGroup/DetachUserController.php
 */

namespace App\Controller\Api\UserGroup;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class DetachUserController
 *
 * @SWG\Tag(name="UserGroup Management")
 *
 * @package App\Controller\Api\UserGroup
 */
class DetachUserController
{
    private UserGroupResource $userGroupResource;
    private SerializerInterface $serializer;

    /**
     * Constructor
     */
    public function __construct(UserGroupResource $userGroupResource, SerializerInterface $serializer)
    {
        $this->userGroupResource = $userGroupResource;
        $this->serializer = $serializer;
    }

    /**
     * Detach specified user from specified user group, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/user_group/{userGroup}/user/{user}",
     *      requirements={
     *          "userGroupId" = "%app.uuid_v1_regex%",
     *          "userId" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"DELETE"},
     *  )
     *
     * @ParamConverter(
     *      "userGroup",
     *      class="App\Resource\UserGroupResource",
     *  )
     * @ParamConverter(
     *      "user",
     *      class="App\Resource\UserResource",
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @SWG\Parameter(
     *      type="string",
     *      name="userGroupId",
     *      in="path",
     *      required=true,
     *      description="User Group GUID",
     *      default="User Group GUID",
     *  )
     * @SWG\Parameter(
     *      type="string",
     *      name="userId",
     *      in="path",
     *      required=true,
     *      description="User GUID",
     *      default="User GUID",
     *  )
     * @SWG\Response(
     *      response=200,
     *      description="Users",
     * @SWG\Schema(
     *          type="array",
     * @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\User::class,
     *                  groups={"User"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(UserGroup $userGroup, User $user): JsonResponse
    {
        $this->userGroupResource->save($userGroup->removeUser($user));
        $groups = [
            'groups' => [
                'set.UserBasic',
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($userGroup->getUsers()->getValues(), 'json', $groups),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
