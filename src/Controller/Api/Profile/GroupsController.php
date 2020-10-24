<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Profile/GroupsController.php
 */

namespace App\Controller\Api\Profile;

use App\Entity\User;
use App\Entity\UserGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class GroupsController
 *
 * @OA\Tag(name="Profile")
 *
 * @package App\Controller\Api\Profile
 */
class GroupsController
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
     * Get current user user groups, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/profile/groups",
     *     methods={"GET"}
     *  );
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @OA\Response(
     *     response=200,
     *     description="User groups",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=UserGroup::class,
     *                 groups={"set.UserProfileGroups"},
     *             ),
     *         ),
     *      ),
     *  )
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
     *        type="object",
     *        example={"code": 403, "message": "Access denied"},
     *        @OA\Property(property="code", type="integer", description="Error code"),
     *        @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     */
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize(
                $loggedInUser->getUserGroups()->toArray(),
                'json',
                ['groups' => 'set.UserProfileGroups']
            ),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
