<?php

declare(strict_types=1);

namespace App\Tests\Application\ApiKey\Transport\Controller\Api\V1;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey;
use App\ApiKey\Infrastructure\DataFixtures\ORM\LoadApiKeyData;
use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class ApiKeyControllerTest extends WebTestCase
{
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/api_key';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/api_key` request returns `401` for non-logged user.')]
    public function testThatGetBaseRouteReturn401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', static::$baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderActions')]
    #[TestDox('Test that `$method $action` returns forbidden error for non-root user.')]
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
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/api_key/count` for the root user returns success response.')]
    public function testThatCountActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request(method: 'GET', uri: static::$baseUrl . '/count');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('count', $responseData);
        self::assertGreaterThan(0, $responseData['count']);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatFindActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/api_key` returns `$responseCode` with login: `$login`, password: `$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', static::$baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);

        if ($responseCode === Response::HTTP_OK) {
            self::assertIsArray($responseData);
            self::assertGreaterThan(5, count($responseData));
            self::assertIsArray($responseData[0]);
            $this->checkBasicFieldsInResponse($responseData[0]);
        }
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /api/v1/api_key` (create api-key) for the root user returns success response.')]
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'description' => 'test api key',
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-api'),
            ],
        ];
        $client->request(method: 'POST', uri: static::$baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($requestData['description'], $responseData['description']);

        // let's check db state
        $resource = static::getContainer()->get(ApiKeyResource::class);
        $apiKeyCreatedEntity = $resource->findOne((string)$responseData['id']);
        self::assertInstanceOf(ApiKey::class, $apiKeyCreatedEntity);
        self::assertCount(1, $apiKeyCreatedEntity->getUserGroups());
        $apiKeyUserGroup = $apiKeyCreatedEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUserGroup);
        self::assertSame($requestData['userGroups'][0], $apiKeyUserGroup->getId());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/api_key/{id}` for the root user returns success response.')]
    public function testThatFindOneActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $resource = static::getContainer()->get(ApiKeyResource::class);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'ApiKey Description: api',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);

        $client->request('GET', static::$baseUrl . '/' . $apiKeyEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($apiKeyEntity->getToken(), $responseData['token']);
        self::assertEquals($apiKeyEntity->getDescription(), $responseData['description']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PUT /api/v1/api_key/{id}` for the root user returns success response.')]
    public function testThatUpdateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $resource = static::getContainer()->get(ApiKeyResource::class);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'ApiKey Description: api',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);
        $requestData = [
            'description' => 'ApiKey Description: api',
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-logged'),
            ],
        ];

        $client->request(
            method: 'PUT',
            uri: static::$baseUrl . '/' . $apiKeyEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
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
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /api/v1/api_key/{id}` for the root user returns success response.')]
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $resource = static::getContainer()->get(ApiKeyResource::class);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'ApiKey Description: api',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);
        $apiKeyUserGroup = $apiKeyEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUserGroup);
        $requestData = [
            'description' => 'ApiKey Description: api edited',
        ];

        $client->request(
            method: 'PATCH',
            uri: static::$baseUrl . '/' . $apiKeyEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($apiKeyEntity->getToken(), $responseData['token']);
        self::assertEquals('ApiKey Description: api edited', $responseData['description']);
        // tet's check that after patch the entity we have the same userGroup as before
        $apiKeyUpdatedEntity = $resource->findOne((string)$responseData['id']);
        self::assertInstanceOf(ApiKey::class, $apiKeyUpdatedEntity);
        self::assertCount(1, $apiKeyUpdatedEntity->getUserGroups());
        $apiKeyUpdatedUserGroup = $apiKeyUpdatedEntity->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $apiKeyUpdatedUserGroup);
        self::assertSame($apiKeyUserGroup->getId(), $apiKeyUpdatedUserGroup->getId());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/api_key/{id}` for the root user returns success response.')]
    public function testThatDeleteActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $resource = static::getContainer()->get(ApiKeyResource::class);
        $apiKeyEntity = $resource->findOneBy([
            'description' => 'ApiKey Description: api',
        ]);
        self::assertInstanceOf(ApiKey::class, $apiKeyEntity);

        $client->request('DELETE', static::$baseUrl . '/' . $apiKeyEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/api_key/ids` for the root user returns success response.')]
    public function testThatIdsActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('GET', static::$baseUrl . '/ids');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertGreaterThan(5, count($responseData));
        self::assertIsString($responseData[0]);
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public static function dataProviderTestThatFindActionWorksAsExpected(): Generator
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
    public static function dataProviderActions(): Generator
    {
        yield ['GET', static::$baseUrl . '/count'];
        yield ['GET', static::$baseUrl];
        yield ['POST', static::$baseUrl];
        yield ['GET', static::$baseUrl . '/' . LoadApiKeyData::getUuidByKey('-root')];
        yield ['PUT', static::$baseUrl . '/' . LoadApiKeyData::getUuidByKey('-root')];
        yield ['DELETE', static::$baseUrl . '/' . LoadApiKeyData::getUuidByKey('-root')];
        yield ['PATCH', static::$baseUrl . '/' . LoadApiKeyData::getUuidByKey('-root')];
        yield ['GET', static::$baseUrl . '/ids'];
    }

    /**
     * @param array<string, string> $responseData
     */
    private function checkBasicFieldsInResponse(array $responseData): void
    {
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
    }
}
