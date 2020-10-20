<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/User/UserGroupsController.php
 */

namespace App\Controller\Api\User;

use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserGroupsController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\Controller\Api\User
 */
class UserGroupsController
{
    private SerializerInterface $serializer;

    /**
     * Constructor
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Fetch specified user user groups, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     *
     * @Route(
     *     "/user/{requestUser}/groups",
     *     requirements={
     *         "requestUser" = "%app.uuid_v1_regex%",
     *     },
     *     methods={"GET"},
     *  )
     *
     * @ParamConverter(
     *     "requestUser",
     *     class="App\Resource\UserResource"
     *  )
     *
     * @Security("is_granted('IS_USER_HIMSELF', requestUser) or is_granted('ROLE_ROOT')")
     *
     * @OA\Response(
     *     response=200,
     *     description="User groups",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=App\Entity\UserGroup::class,
     *                 groups={"UserGroup", "UserGroup.role"},
     *             ),
     *         ),
     *     ),
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\Schema(
     *         type="object",
     *         example={
     *             "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *             "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *         },
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     *  )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\Schema(
     *         type="object",
     *         example={
     *             "Access denied": "{code: 403, message: 'Access denied'}",
     *         },
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     *  )
     */
    public function __invoke(User $requestUser): JsonResponse
    {
        $groups = [
            'groups' => [
                'set.UserGroupBasic',
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($requestUser->getUserGroups()->getValues(), 'json', $groups),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
