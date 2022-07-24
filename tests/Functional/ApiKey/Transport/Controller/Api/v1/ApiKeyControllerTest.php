<?php

declare(strict_types=1);

namespace App\Tests\Functional\ApiKey\Transport\Controller\Api\v1;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey;
use App\ApiKey\Infrastructure\DataFixtures\ORM\LoadApiKeyData;
use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class ApiKeyControllerTest
 *
 * @package App\Tests
 */
class ApiKeyControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/api_key';

    /**
     * @testdox Test that `GET /v1/api_key` request returns `401` for non-logged user.
     *
     * @throws Throwable
     */
    public function testThatGetBaseRouteReturn401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `$method $action` returns forbidden error for non-root user.
     *
     * @dataProvider dataProviderActions
     *
     * @throws Throwable
     */
    public function testThatAllActionsForbiddenForNonRootUser(string $method, string $action): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request($method, $action);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `GET /v1/api_key` returns `$responseCode` with login: `$login`, password: `$password`.
     *
     * @dataProvider dataProviderTestThatFindActionWorksAsExpected
     *
     * @throws Throwable
     */
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `POST /v1/api_key` (create api-key) for the Root user returns success response.
     *
     * @throws Throwable
     */
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'description' => 'test api key',
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-api'],
            ],
        ];
        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($requestData['description'], $responseData['description']);
    }

    /**
     * @testdox Test that `GET /v1/api_key/{id}` for the Root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatFindOneActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $resource = static::getContainer()->get(ApiKeyResource::class);
        static::assertInstanceOf(ApiKeyResource::class, $resource);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'test api key',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);

        $client->request('GET', $this->baseUrl . '/' . $apiKeyEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($apiKeyEntity->getToken(), $responseData['token']);
        self::assertEquals($apiKeyEntity->getDescription(), $responseData['description']);
    }

    /**
     * @testdox Test that `PUT /v1/api_key/{id}` for the Root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatUpdateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $resource = static::getContainer()->get(ApiKeyResource::class);
        static::assertInstanceOf(ApiKeyResource::class, $resource);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'test api key',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);
        $requestData = [
            'description' => 'test api key',
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-logged'],
            ],
        ];

        $client->request(
            method: 'PUT',
            uri: $this->baseUrl . '/' . $apiKeyEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($apiKeyEntity->getToken(), $responseData['token']);
        self::assertEquals($apiKeyEntity->getDescription(), $responseData['description']);
        // let's check that after updating the entity we have new userGroup
        $apiKeyUpdatedEntity = $resource->findOne((string)$responseData['id']);
        self::assertInstanceOf(ApiKey::class, $apiKeyUpdatedEntity);
        self::assertCount(1, $apiKeyUpdatedEntity->getUserGroups());
        $apiKeyUserGroup = $apiKeyUpdatedEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUserGroup);
        self::assertSame($requestData['userGroups'][0], $apiKeyUserGroup->getId());
    }

    /**
     * @testdox Test that `PATCH /v1/api_key/{id}` for the Root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $resource = static::getContainer()->get(ApiKeyResource::class);
        static::assertInstanceOf(ApiKeyResource::class, $resource);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'test api key',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);
        $apiKeyUserGroup = $apiKeyEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUserGroup);
        $requestData = [
            'description' => 'test api key edited',
        ];

        $client->request(
            method: 'PATCH',
            uri: $this->baseUrl . '/' . $apiKeyEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($apiKeyEntity->getToken(), $responseData['token']);
        self::assertEquals('test api key edited', $responseData['description']);
        // tet's check that after patch the entity we have the same userGroup as before
        $apiKeyUpdatedEntity = $resource->findOne((string)$responseData['id']);
        self::assertInstanceOf(ApiKey::class, $apiKeyUpdatedEntity);
        self::assertCount(1, $apiKeyUpdatedEntity->getUserGroups());
        $apiKeyUpdatedUserGroup = $apiKeyUpdatedEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUpdatedUserGroup);
        self::assertSame($apiKeyUserGroup->getId(), $apiKeyUpdatedUserGroup->getId());
    }

    /**
     * @testdox Test that `DELETE /v1/api_key/{id}` for the Root user returns success response.
     *
     * @depends testThatPatchActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatDeleteActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $resource = static::getContainer()->get(ApiKeyResource::class);
        static::assertInstanceOf(ApiKeyResource::class, $resource);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'test api key edited',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);

        $client->request('DELETE', $this->baseUrl . '/' . $apiKeyEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
    }

    /**
     * @testdox Test that `GET /v1/api_key/ids` for the Root user returns success response.
     *
     * @throws Throwable
     */
    public function testThatIdsActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('GET', $this->baseUrl . '/ids');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public function dataProviderTestThatFindActionWorksAsExpected(): Generator
    {
        // username === login
        yield ['john', 'password', Response::HTTP_FORBIDDEN];
        yield ['john-logged', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john-api', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john-user', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john-admin', 'password-admin', Response::HTTP_FORBIDDEN];
        yield ['john-root', 'password-root', Response::HTTP_OK];

        // email === login
        yield ['john.doe@test.com', 'password', Response::HTTP_FORBIDDEN];
        yield ['john.doe-logged@test.com', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john.doe-api@test.com', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john.doe-user@test.com', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john.doe-admin@test.com', 'password-admin', Response::HTTP_FORBIDDEN];
        yield ['john.doe-root@test.com', 'password-root', Response::HTTP_OK];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderActions(): Generator
    {
        yield ['GET', $this->baseUrl . '/count'];
        yield ['GET', $this->baseUrl];
        yield ['POST', $this->baseUrl];
        yield ['GET', $this->baseUrl . '/' . LoadApiKeyData::$uuids['-root']];
        yield ['PUT', $this->baseUrl . '/' . LoadApiKeyData::$uuids['-root']];
        yield ['DELETE', $this->baseUrl . '/' . LoadApiKeyData::$uuids['-root']];
        yield ['PATCH', $this->baseUrl . '/' . LoadApiKeyData::$uuids['-root']];
        yield ['GET', $this->baseUrl . '/ids'];
    }
}
