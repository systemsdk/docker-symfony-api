<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\Profile;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Application\Security\RolesService;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class IndexControllerTest
 *
 * @package App\Tests
 */
class IndexControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/profile';

    /**
     * @testdox Test that `GET /api/v1/profile` for the `john-root` user returns success response.
     *
     * @throws Throwable
     */
    public function testThatGetUserProfileActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $roleService = static::getContainer()->get(RolesService::class);
        static::assertInstanceOf(RolesService::class, $roleService);
        $resource = static::getContainer()->get(UserResource::class);
        static::assertInstanceOf(UserResource::class, $resource);
        $userEntity = $resource->findOneBy([
            'username' => 'john-root',
        ]);
        self::assertInstanceOf(User::class, $userEntity);

        $client->request(method: 'GET', uri: $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('id', $responseData);
        self::assertEquals($userEntity->getId(), $responseData['id']);
        self::assertArrayHasKey('username', $responseData);
        self::assertEquals($userEntity->getUsername(), $responseData['username']);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertEquals($userEntity->getFirstName(), $responseData['firstName']);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertEquals($userEntity->getLastName(), $responseData['lastName']);
        self::assertArrayHasKey('email', $responseData);
        self::assertEquals($userEntity->getEmail(), $responseData['email']);
        self::assertArrayHasKey('language', $responseData);
        self::assertEquals($userEntity->getLanguage(), $responseData['language']);
        self::assertArrayHasKey('locale', $responseData);
        self::assertEquals($userEntity->getLocale(), $responseData['locale']);
        self::assertArrayHasKey('timezone', $responseData);
        self::assertEquals($userEntity->getTimezone(), $responseData['timezone']);
        self::assertArrayHasKey('roles', $responseData);
        self::assertIsArray($responseData['roles']);
        self::assertCount(count($roleService->getInheritedRoles($userEntity->getRoles())), $responseData['roles']);
    }

    /**
     * @testdox Test that `GET /api/v1/profile` for non-logged user returns error response.
     *
     * @throws Throwable
     */
    public function testThatGetGetUserProfileActionForNonLoggedUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient();

        $client->request(method: 'GET', uri: $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }
}
