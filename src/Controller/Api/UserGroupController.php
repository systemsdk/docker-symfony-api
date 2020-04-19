<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/UserGroupController.php
 */

namespace App\Controller\Api;

use App\Rest\ResponseHandler;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use App\Rest\Controller;
use App\DTO\UserGroup\UserGroupCreate;
use App\DTO\UserGroup\UserGroupPatch;
use App\DTO\UserGroup\UserGroupUpdate;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use App\Resource\UserResource;
use App\Rest\Traits\Actions;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class UserGroupController
 *
 * @Route(
 *     path="/user_group",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @SWG\Tag(name="UserGroup Management")
 *
 * @package App\Controller\Api
 *
 * @method UserGroupResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
class UserGroupController extends Controller
{
    // Traits for REST actions
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\FindOneAction;
    use Actions\Admin\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => UserGroupCreate::class,
        Controller::METHOD_UPDATE => UserGroupUpdate::class,
        Controller::METHOD_PATCH => UserGroupPatch::class,
    ];

    /**
     * Constructor
     *
     * @param UserGroupResource $resource
     */
    public function __construct(UserGroupResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * List specified user group users, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *      "/{userGroup}/users",
     *      requirements={
     *          "userGroup" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @ParamConverter(
     *      "userGroup",
     *      class="App\Resource\UserGroupResource",
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="User group users",
     *      @SWG\Schema(
     *          ref=@Model(
     *              type=User::class,
     *              groups={"User", "User.userGroups", "User.roles", "UserGroup", "UserGroup.role"},
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
     *      response=404,
     *      description="User Group not found",
     *  )
     *
     * @param Request      $request
     * @param UserResource $userResource
     * @param UserGroup    $userGroup
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function getUserGroupUsersAction(
        Request $request,
        UserResource $userResource,
        UserGroup $userGroup
    ): Response {
        return $this
            ->getResponseHandler()
            ->createResponse($request, $userResource->getUsersForGroup($userGroup), $userResource);
    }

    /**
     * Attach specified user to specified user group, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{userGroup}/user/{user}",
     *      requirements={
     *          "userGroup" = "%app.uuid_v1_regex%",
     *          "user" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"POST"},
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
     *      description="List of user group users - specified user already exists on this group",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\User::class,
     *                  groups={"User"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=201,
     *      description="List of user group users - specified user has been attached to this group",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
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
     * @param UserGroup           $userGroup
     * @param User                $user
     * @param SerializerInterface $serializer
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function attachUserAction(UserGroup $userGroup, User $user, SerializerInterface $serializer): JsonResponse
    {
        $status = $userGroup->getUsers()->contains($user) ? Response::HTTP_OK : Response::HTTP_CREATED;
        $this->getResource()->save($userGroup->addUser($user));

        return $this->getUserResponse($userGroup, $serializer, $status);
    }

    /**
     * Detach specified user from specified user group, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{userGroup}/user/{user}",
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
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
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
     * @param UserGroup           $userGroup
     * @param User                $user
     * @param SerializerInterface $serializer
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function detachUserAction(UserGroup $userGroup, User $user, SerializerInterface $serializer): JsonResponse
    {
        $this->getResource()->save($userGroup->removeUser($user));

        return $this->getUserResponse($userGroup, $serializer);
    }

    /**
     * Helper method to create User response, accessible for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @param UserGroup           $userGroup
     * @param SerializerInterface $serializer
     * @param int|null            $status
     *
     * @return JsonResponse
     */
    private function getUserResponse(
        UserGroup $userGroup,
        SerializerInterface $serializer,
        ?int $status = null
    ): JsonResponse {
        $status ??= Response::HTTP_OK;
        $groups = [
            'groups' => [
                'User',
            ],
        ];

        return new JsonResponse(
            $serializer->serialize($userGroup->getUsers()->getValues(), 'json', $groups),
            $status,
            [],
            true
        );
    }
}
