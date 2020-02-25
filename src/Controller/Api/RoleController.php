<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/RoleController.php
 */

namespace App\Controller\Api;

use App\Rest\ResponseHandler;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use App\Rest\Controller;
use App\Resource\RoleResource;
use App\Annotation\RestApiDoc;
use App\Entity\Role;
use App\Rest\Traits\Actions;
use App\Rest\Traits\Methods;
use App\Security\RolesService;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class RoleController
 *
 * @Route(
 *     path="/role",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @SWG\Tag(name="Role Management")
 *
 * @package App\Controller\Api
 *
 * @method RoleResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
class RoleController extends Controller
{
    // Traits for REST actions
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\IdsAction;
    use Methods\FindOneMethod;

    /**
     * Constructor
     *
     * @param RoleResource $resource
     */
    public function __construct(RoleResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * Find role entity, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *      path="/{role}",
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
     *      @SWG\Schema(
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
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Request $request
     * @param string  $role
     *
     * @throws LogicException
     * @throws Throwable
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     *
     * @return Response
     */
    public function findOneAction(Request $request, string $role): Response
    {
        return $this->findOneMethod($request, $role);
    }

    /**
     * Return all inherited roles as an array for specified Role, accessible for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *      "/{role}/inherited",
     *      requirements={
     *          "role" = "^ROLE_\w+$",
     *      },
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
     * @SWG\Response(
     *      response=200,
     *      description="Inherited roles",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=Role::class,
     *                  groups={"Role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Role         $role
     * @param RolesService $rolesService
     *
     * @return JsonResponse
     */
    public function getInheritedRolesAction(Role $role, RolesService $rolesService): JsonResponse
    {
        return new JsonResponse($rolesService->getInheritedRoles([$role->getId()]));
    }
}
