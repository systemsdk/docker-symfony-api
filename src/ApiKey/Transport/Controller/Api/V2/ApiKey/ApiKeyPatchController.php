<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\V2\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyPatch;
use App\ApiKey\Application\Resource\ApiKeyPatchResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyPatchResourceInterface;
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
 * @method ApiKeyPatchResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'ApiKey Management v2')]
class ApiKeyPatchController extends Controller
{
    use Actions\Root\PatchAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_PATCH => ApiKeyPatch::class,
    ];

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyPatchResource $resource
     */
    public function __construct(
        ApiKeyPatchResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
