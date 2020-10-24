<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Role/RoleController.php
 */

namespace App\Controller\Api\Role;

use App\Entity\Role;
use App\Security\RolesService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InheritedRolesController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Controller\Api\Role
 */
class InheritedRolesController
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
     * Return all inherited roles as an array for specified Role, accessible for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     "/role/{role}/inherited",
     *     requirements={
     *         "role" = "^ROLE_\w+$",
     *     },
     *     methods={"GET"}
     *  )
     *
     * @ParamConverter(
     *     "role",
     *     class="App\Resource\RoleResource"
     * )
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @OA\Response(
     *     response=200,
     *     description="Inherited roles",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=Role::class,
     *                 groups={"Role"},
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
     */
    public function __invoke(Role $role): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles([$role->getId()]));
    }
}
