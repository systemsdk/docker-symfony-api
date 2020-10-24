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
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class DetachUserController
 *
 * @OA\Tag(name="UserGroup Management")
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
     *     "userGroup",
     *     class="App\Resource\UserGroupResource",
     *  )
     * @ParamConverter(
     *     "user",
     *     class="App\Resource\UserResource",
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @OA\Parameter(
     *     name="userGroup",
     *     in="path",
     *     required=true,
     *     description="User Group GUID",
     *     @OA\Schema(
     *         type="string",
     *         default="User Group GUID",
     *     )
     * )
     * @OA\Parameter(
     *     name="user",
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
     *     description="Users",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=App\Entity\User::class,
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
