<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Profile/IndexController.php
 */

namespace App\Controller\Api\Profile;

use App\Entity\User;
use App\Security\RolesService;
use App\Utils\JSON;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class IndexController
 *
 * @SWG\Tag(name="Profile")
 *
 * @package App\Controller\Api\Profile
 */
class IndexController
{
    private SerializerInterface $serializer;
    private RolesService $rolesService;

    /**
     * Constructor
     */
    public function __construct(SerializerInterface $serializer, RolesService $rolesService)
    {
        $this->serializer = $serializer;
        $this->rolesService = $rolesService;
    }

    /**
     * Get current user profile data, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/profile",
     *     methods={"GET"}
     *  );
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="User profile data",
     * @SWG\Schema(
     *          ref=@Model(
     *              type=User::class,
     *              groups={"set.UserProfile"},
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
     *
     * @throws JsonException
     */
    public function __invoke(User $loggedInUser): JsonResponse
    {
        /** @var array<string, string|array> $output */
        $output = JSON::decode(
            $this->serializer->serialize($loggedInUser, 'json', ['groups' => 'set.UserProfile']),
            true
        );
        /** @var array<int, string> $roles */
        $roles = $output['roles'];
        $output['roles'] = $this->rolesService->getInheritedRoles($roles);

        return new JsonResponse($output);
    }
}
