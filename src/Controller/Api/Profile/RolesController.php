<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Profile/RolesController.php
 */

namespace App\Controller\Api\Profile;

use App\Entity\Role;
use App\Entity\User;
use App\Security\RolesService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RolesController
 *
 * @OA\Tag(name="Profile")
 *
 * @package App\Controller\Api\Profile
 */
class RolesController
{
    private RolesService $rolesService;

    /**
     * Constructor
     */
    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    /**
     * Get current user roles as an array, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/profile/roles",
     *     methods={"GET"},
     *  );
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @OA\Response(
     *     response=200,
     *     description="User roles",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=Role::class,
     *                 groups={"Role"},
     *             ),
     *         ),
     *     ),
     *  )
     * @OA\Response(
     *     response=401,
     *     description="Invalid token",
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
     */
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles($loggedInUser->getRoles()));
    }
}
