<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Root;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'updateAction' for REST controllers for 'ROLE_ROOT' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\UpdateMethod for detailed documents.
 *
 * @package App\General
 */
trait UpdateAction
{
    use UpdateMethod;

    /**
     * Update entity with new data, accessible only for 'ROLE_ROOT' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_PUT],
    )]
    #[IsGranted(Role::ROOT->value)]
    #[OA\RequestBody(
        request: 'body',
        description: 'object',
        content: new JsonContent(
            type: 'object',
            example: [
                'param' => 'value',
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            type: 'object',
            example: [],
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
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }
}
