<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Admin;

use App\General\Transport\Rest\Traits\Methods\FindMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'findAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\General
 */
trait FindAction
{
    use FindMethod;

    /**
     * Get list of entities, accessible only for 'ROLE_ADMIN' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::ADMIN->value)]
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
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
