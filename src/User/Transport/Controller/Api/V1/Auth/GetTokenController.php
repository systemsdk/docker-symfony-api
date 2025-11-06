<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Auth;

use App\General\Domain\Utils\JSON;
use App\User\Domain\Entity\User;
use JsonException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

use function sprintf;

/**
 * @package App\User
 */
#[AsController]
#[OA\Tag(name: 'Authentication')]
class GetTokenController
{
    /**
     * Get user Json Web Token (JWT) for authentication.
     *
     * @throws HttpException
     * @throws JsonException
     */
    #[Route(
        path: '/v1/auth/get_token',
        methods: [Request::METHOD_POST],
    )]
    #[OA\RequestBody(
        request: 'body',
        description: 'Credentials object',
        required: true,
        content: new JsonContent(
            properties: [
                new Property(property: 'username', ref: new Model(type: User::class, groups: ['User.username'])),
                new Property(property: 'password', type: 'string'),
            ],
            type: 'object',
            example: [
                'username' => 'username',
                'password' => 'password',
            ],
        ),
    )]
    #[OA\Post(
        security: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'JSON Web Token for user',
                content: new JsonContent(
                    properties: [
                        new Property(
                            property: 'token',
                            description: 'Json Web Token',
                            type: 'string',
                        ),
                    ],
                    type: 'object',
                    example: [
                        'token' => '_json_web_token_',
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'code', description: 'Error code', type: 'integer'),
                        new Property(property: 'message', description: 'Error description', type: 'string'),
                    ],
                    type: 'object',
                    example: [
                        'code' => 400,
                        'message' => 'Bad Request',
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'code', description: 'Error code', type: 'integer'),
                        new Property(property: 'message', description: 'Error description', type: 'string'),
                    ],
                    type: 'object',
                    example: [
                        'code' => 401,
                        'message' => 'Bad credentials',
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(): never
    {
        $message = sprintf(
            'You need to send JSON body to obtain token eg. %s',
            JSON::encode([
                'username' => 'username',
                'password' => 'password',
            ]),
        );

        throw new HttpException(Response::HTTP_BAD_REQUEST, $message);
    }
}
