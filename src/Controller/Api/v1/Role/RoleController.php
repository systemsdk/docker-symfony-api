<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Role;

use App\Resource\RoleResource;
use App\Rest\Controller;
use App\Rest\ResponseHandler;
use App\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class RoleController
 *
 * @OA\Tag(name="Role Management")
 *
 * @package App\Controller\Api\v1\Role
 *
 * @method RoleResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v1/role',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class RoleController extends Controller
{
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\IdsAction;

    public function __construct(
        protected RoleResource $resource,
    ) {
    }
}
