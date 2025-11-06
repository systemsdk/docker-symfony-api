<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\V1\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyPatch;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyUpdate;
use App\ApiKey\Application\Resource\ApiKeyResource;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\ApiKey
 *
 * @method ApiKeyResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(
    path: '/v1/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'ApiKey Management v1')]
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
        ApiKeyResource $resource,
    ) {
        parent::__construct($resource);
    }
}
