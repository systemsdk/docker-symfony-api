<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\v2\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyFindOneResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyFindOneResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class ApiKeyViewController
 *
 * @OA\Tag(name="ApiKey Management v2")
 *
 * @package App\ApiKey
 *
 * @method ApiKeyFindOneResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class ApiKeyViewController extends Controller
{
    use Actions\Root\FindOneAction;

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyFindOneResource $resource
     */
    public function __construct(
        ApiKeyFindOneResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
