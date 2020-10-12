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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class FindOneRoleController
 *
 * @SWG\Tag(name="Role Management")
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
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      examples={
     *          "success": "{id: 'ROLE_ROOT', description: 'role root description'}"
     *      },
     * @SWG\Schema(
     *          ref=@Model(
     *              type=Role::class,
     *              groups={"Role"},
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     * @SWG\Schema(
     *          type="object",
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, string $role): Response
    {
        return $this->findOneMethod($request, $role);
    }
}
