<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/UserGroup/UsersController.php
 */

namespace App\Controller\Api\UserGroup;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserResource;
use App\Rest\ResponseHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class UsersController
 *
 * @OA\Tag(name="UserGroup Management")
 *
 * @package App\Controller\Api\UserGroup
 */
class UsersController
{
    private UserResource $userResource;
    private ResponseHandler $responseHandler;

    /**
     * Constructor
     */
    public function __construct(UserResource $userResource, ResponseHandler $responseHandler)
    {
        $this->userResource = $userResource;
        $this->responseHandler = $responseHandler;
    }

    /**
     * List specified user group users, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *      "/user_group/{userGroup}/users",
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
     * @OA\Response(
     *     response=200,
     *     description="User group users",
     *     @OA\Schema(
     *         ref=@Model(
     *             type=User::class,
     *             groups={"User", "User.userGroups", "User.roles", "UserGroup", "UserGroup.role"},
     *         ),
     *     ),
     * )
     * @OA\Response(
     *     response=401,
     *     description="Invalid token",
     *     @OA\Schema(
     *         example={
     *             "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *             "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *         },
     *     ),
     * )
     * @OA\Response(
     *     response=404,
     *     description="User Group not found",
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, UserGroup $userGroup): Response
    {
        return $this->responseHandler
            ->createResponse($request, $this->userResource->getUsersForGroup($userGroup), $this->userResource);
    }
}
