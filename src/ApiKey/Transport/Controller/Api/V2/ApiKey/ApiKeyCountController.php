<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\V2\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyCountResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyCountResourceInterface;
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
 * @method ApiKeyCountResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'ApiKey Management v2')]
class ApiKeyCountController extends Controller
{
    use Actions\Root\CountAction;

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyCountResource $resource
     */
    public function __construct(
        ApiKeyCountResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
