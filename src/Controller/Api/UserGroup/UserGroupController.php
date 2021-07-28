<?php

declare(strict_types=1);

namespace App\Controller\Api\UserGroup;

use App\DTO\UserGroup\UserGroupCreate;
use App\DTO\UserGroup\UserGroupPatch;
use App\DTO\UserGroup\UserGroupUpdate;
use App\Resource\UserGroupResource;
use App\Rest\Controller;
use App\Rest\ResponseHandler;
use App\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserGroupController
 *
 * @Route(
 *     path="/user_group",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @OA\Tag(name="UserGroup Management")
 *
 * @package App\Controller\Api\UserGroup
 *
 * @method UserGroupResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
class UserGroupController extends Controller
{
    // Traits for REST actions
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\FindOneAction;
    use Actions\Admin\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => UserGroupCreate::class,
        Controller::METHOD_UPDATE => UserGroupUpdate::class,
        Controller::METHOD_PATCH => UserGroupPatch::class,
    ];

    public function __construct(
        protected UserGroupResource $resource,
    ) {
    }
}
