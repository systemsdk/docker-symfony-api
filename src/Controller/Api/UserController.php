<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/UserController.php
 */

namespace App\Controller\Api;

use App\Rest\ResponseHandler;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use App\Rest\Controller;
use App\Annotation\RestApiDoc;
use App\DTO\User\UserCreate;
use App\DTO\User\UserPatch;
use App\DTO\User\UserUpdate;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserResource;
use App\Rest\Traits\Actions;
use App\Rest\Traits\Methods;
use App\Security\RolesService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class UserController
 *
 * @Route(
 *     path="/user",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @SWG\Tag(name="User Management")
 *
 * @package App\Controller\Api
 *
 * @method UserResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
class UserController extends Controller
{
    // Traits for REST actions
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\FindOneAction;
    use Actions\Admin\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;
    use Methods\DeleteMethod;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => UserCreate::class,
        Controller::METHOD_UPDATE => UserUpdate::class,
        Controller::METHOD_PATCH => UserPatch::class,
    ];

    /**
     * Constructor
     *
     * @param UserResource $resource
     */
    public function __construct(UserResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * Delete user entity, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{requestUser}",
     *      requirements={
     *          "requestUser" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"DELETE"},
     *  )
     *
     * @ParamConverter(
     *     "requestUser",
     *     class="App\Resource\UserResource"
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="deleted",
     *      @SWG\Schema(
     *          ref=@Model(
     *              type=User::class,
     *              groups={"User"},
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Request $request
     * @param User    $requestUser
     * @param User    $loggedInUser
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function deleteAction(Request $request, User $requestUser, User $loggedInUser): Response
    {
        if ($loggedInUser === $requestUser) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'You cannot remove yourself...');
        }

        return $this->deleteMethod($request, $requestUser->getId());
    }

    /**
     * Fetch specified user roles, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{requestUser}/roles",
     *      requirements={
     *          "requestUser" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @ParamConverter(
     *     "requestUser",
     *     class="App\Resource\UserResource"
     *  )
     *
     * @Security("is_granted('IS_USER_HIMSELF', requestUser) or is_granted('ROLE_ROOT')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="Specified user roles",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(type="string"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @param User         $requestUser
     * @param RolesService $roles
     *
     * @return JsonResponse
     */
    public function getUserRolesAction(User $requestUser, RolesService $roles): JsonResponse
    {
        return new JsonResponse($roles->getInheritedRoles($requestUser->getRoles()));
    }

    /**
     * Fetch specified user user groups, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{requestUser}/groups",
     *      requirements={
     *          "requestUser" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @ParamConverter(
     *     "requestUser",
     *     class="App\Resource\UserResource"
     *  )
     *
     * @Security("is_granted('IS_USER_HIMSELF', requestUser) or is_granted('ROLE_ROOT')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="User groups",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @param User                $requestUser
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     */
    public function getUserGroupsAction(User $requestUser, SerializerInterface $serializer): JsonResponse
    {
        return $this->getUserGroupResponse($requestUser, $serializer);
    }

    /**
     * Attach specified user group to specified user, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{user}/group/{userGroup}",
     *      requirements={
     *          "user" = "%app.uuid_v1_regex%",
     *          "userGroup" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"POST"},
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
     * @SWG\Parameter(
     *      type="string",
     *      name="userId",
     *      in="path",
     *      required=true,
     *      description="User GUID",
     *      default="User GUID",
     *  )
     * @SWG\Parameter(
     *      type="string",
     *      name="userGroupId",
     *      in="path",
     *      required=true,
     *      description="User Group GUID",
     *      default="User Group GUID",
     *  )
     * @SWG\Response(
     *      response=200,
     *      description="User groups (user already belongs to this group)",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=201,
     *      description="User groups (user added to this group)",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @param User                $user
     * @param UserGroup           $userGroup
     * @param SerializerInterface $serializer
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function attachUserGroupAction(
        User $user,
        UserGroup $userGroup,
        SerializerInterface $serializer
    ): JsonResponse {
        $status = $user->getUserGroups()->contains($userGroup) ? Response::HTTP_OK : Response::HTTP_CREATED;
        $this->getResource()->save($user->addUserGroup($userGroup));

        return $this->getUserGroupResponse($user, $serializer, $status);
    }

    /**
     * Detach specified user group from specified user, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{user}/group/{userGroup}",
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
     * @SWG\Parameter(
     *      type="string",
     *      name="userId",
     *      in="path",
     *      required=true,
     *      description="User GUID",
     *      default="User GUID",
     *  )
     * @SWG\Parameter(
     *      type="string",
     *      name="userGroupId",
     *      in="path",
     *      required=true,
     *      description="User Group GUID",
     *      default="User Group GUID",
     *  )
     * @SWG\Response(
     *      response=200,
     *      description="User groups",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Forbidden",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @param User                $user
     * @param UserGroup           $userGroup
     * @param SerializerInterface $serializer
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function detachUserGroupAction(
        User $user,
        UserGroup $userGroup,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->getResource()->save($user->removeUserGroup($userGroup));

        return $this->getUserGroupResponse($user, $serializer);
    }

    /**
     * Helper method to create UserGroup response, accessible for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @param User                $user
     * @param SerializerInterface $serializer
     * @param int|null            $status
     *
     * @return JsonResponse
     */
    private function getUserGroupResponse(
        User $user,
        SerializerInterface $serializer,
        ?int $status = null
    ): JsonResponse {
        $status ??= Response::HTTP_OK;
        $groups = [
            'groups' => [
                'UserGroup',
                'UserGroup.role',
            ],
        ];

        return new JsonResponse(
            $serializer->serialize($user->getUserGroups()->getValues(), 'json', $groups),
            $status,
            [],
            true
        );
    }
}
