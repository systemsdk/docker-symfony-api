<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Role/RoleController.php
 */

namespace App\Controller\Api\Role;

use App\Resource\RoleResource;
use App\Rest\Controller;
use App\Rest\ResponseHandler;
use App\Rest\Traits\Actions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

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
 * @package App\Controller\Api\Role
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

    /**
     * Constructor
     */
    public function __construct(RoleResource $resource)
    {
        $this->setResource($resource);
    }
}
