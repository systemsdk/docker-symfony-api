<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\v2\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyDeleteResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyDeleteResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class ApiKeyDeleteController
 *
 * @OA\Tag(name="ApiKey Management v2")
 *
 * @package App\ApiKey
 *
 * @method ApiKeyDeleteResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class ApiKeyDeleteController extends Controller
{
    use Actions\Root\DeleteAction;

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyDeleteResource $resource
     */
    public function __construct(
        ApiKeyDeleteResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
