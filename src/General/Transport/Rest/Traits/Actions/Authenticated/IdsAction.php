<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Authenticated;

use App\General\Transport\Rest\Traits\Methods\IdsMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'idsAction' for REST controllers for authenticated users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\General
 */
trait IdsAction
{
    use IdsMethod;

    /**
     * Find ids list, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/ids',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', description: 'Error code', type: 'integer'),
                new Property(property: 'message', description: 'Error description', type: 'string'),
            ],
            type: 'object',
            example: [
                'code' => 403,
                'message' => 'Access denied',
            ],
        ),
    )]
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
