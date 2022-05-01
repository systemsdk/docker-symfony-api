<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\v1\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyPatch;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyUpdate;
use App\ApiKey\Application\Resource\ApiKeyResource;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class ApiKeyController
 *
 * @OA\Tag(name="ApiKey Management v1")
 *
 * @package App\ApiKey
 *
 * @method ApiKeyResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v1/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class ApiKeyController extends Controller
{
    use Actions\Root\CountAction;
    use Actions\Root\FindAction;
    use Actions\Root\FindOneAction;
    use Actions\Root\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ApiKeyCreate::class,
        Controller::METHOD_UPDATE => ApiKeyUpdate::class,
        Controller::METHOD_PATCH => ApiKeyPatch::class,
    ];

    public function __construct(
        protected ApiKeyResource $resource,
    ) {
    }
}
