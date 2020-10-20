<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Role/FindOneRoleController.php
 */

namespace App\Controller\Api\Role;

use App\Entity\Role;
use App\Resource\RoleResource;
use App\Rest\Controller;
use App\Rest\Traits\Methods;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class FindOneRoleController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Controller\Api\Role
 */
class FindOneRoleController extends Controller
{
    use Methods\FindOneMethod;

    /**
     * Constructor
     */
    public function __construct(RoleResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * Find role entity, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *      path="/role/{role}",
     *      requirements={
     *          "role" = "^ROLE_\w+$",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\Schema(
     *          example={
     *              "success": "{id: 'ROLE_ROOT', description: 'role root description'}"
     *          },
     *          ref=@Model(
     *              type=Role::class,
     *              groups={"Role"},
     *          ),
     *      ),
     * )
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
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, string $role): Response
    {
        return $this->findOneMethod($request, $role);
    }
}
