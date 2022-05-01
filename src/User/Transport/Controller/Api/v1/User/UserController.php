<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\v1\User;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\User\Application\DTO\User\UserCreate;
use App\User\Application\DTO\User\UserPatch;
use App\User\Application\DTO\User\UserUpdate;
use App\User\Application\Resource\UserResource;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class UserController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\User
 *
 * @method UserResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v1/user',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class UserController extends Controller
{
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\FindOneAction;
    use Actions\Admin\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => UserCreate::class,
        Controller::METHOD_UPDATE => UserUpdate::class,
        Controller::METHOD_PATCH => UserPatch::class,
    ];

    public function __construct(
        protected UserResource $resource,
    ) {
    }
}
