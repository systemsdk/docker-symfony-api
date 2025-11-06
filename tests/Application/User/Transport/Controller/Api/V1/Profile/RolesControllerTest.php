<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Security\RolesService;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class RolesControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/profile';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile/roles` for the `john-root` user returns success response.')]
    public function testThatGetUserRolesActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $roleService = static::getContainer()->get(RolesService::class);
        $resource = static::getContainer()->get(UserResource::class);
        $userEntity = $resource->findOneBy([
            'username' => 'john-root',
        ]);
        self::assertInstanceOf(User::class, $userEntity);

        $client->request(method: 'GET', uri: $this->baseUrl . '/roles');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertIsString($responseData[0]);
        self::assertCount(count($roleService->getInheritedRoles($userEntity->getRoles())), $responseData);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile/roles` for non-logged user returns error response.')]
    public function testThatGetGetUserRolesActionForNonLoggedUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient();

        $client->request(method: 'GET', uri: $this->baseUrl . '/roles');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }
}
