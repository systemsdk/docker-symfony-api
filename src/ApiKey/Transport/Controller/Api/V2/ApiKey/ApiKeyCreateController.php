<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Controller\Api\V2\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate;
use App\ApiKey\Application\Resource\ApiKeyCreateResource;
use App\ApiKey\Application\Resource\Interfaces\ApiKeyCreateResourceInterface;
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
 * @method ApiKeyCreateResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(
    path: '/v2/api_key',
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'ApiKey Management v2')]
class ApiKeyCreateController extends Controller
{
    use Actions\Root\CreateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ApiKeyCreate::class,
    ];

    /**
     * @param \App\ApiKey\Application\Resource\ApiKeyCreateResource $resource
     */
    public function __construct(
        ApiKeyCreateResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
