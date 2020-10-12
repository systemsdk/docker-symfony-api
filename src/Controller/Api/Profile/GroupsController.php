<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Profile/GroupsController.php
 */

namespace App\Controller\Api\Profile;

use App\Entity\User;
use App\Entity\UserGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class GroupsController
 *
 * @SWG\Tag(name="Profile")
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
     * @SWG\Response(
     *      response=200,
     *      description="User groups",
     * @SWG\Schema(
     *          type="array",
     * @SWG\Items(
     *              ref=@Model(
     *                  type=UserGroup::class,
     *                  groups={"set.UserProfileGroups"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     * @SWG\Schema(
     *          type="object",
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     * @SWG\Schema(
     *          type="object",
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *  )
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
