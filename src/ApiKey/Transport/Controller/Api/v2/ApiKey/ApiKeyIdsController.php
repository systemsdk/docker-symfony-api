<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\v2\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyIdsResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyIdsResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class ApiKeyIdsController
 *
 * @OA\Tag(name="ApiKey Management v2")
 *
 * @package App\ApiKey
 *
 * @method ApiKeyIdsResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class ApiKeyIdsController extends Controller
{
    use Actions\Root\IdsAction;

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyIdsResource $resource
     */
    public function __construct(
        ApiKeyIdsResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
