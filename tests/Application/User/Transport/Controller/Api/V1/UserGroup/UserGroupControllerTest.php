<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\UserGroup;

use App\General\Domain\Utils\JSON;
use App\Role\Domain\Enum\Role;
use App\Tests\Application\User\Transport\Controller\Api\V1\Traits\UserHelper;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserData;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Exception;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class UserGroupControllerTest extends WebTestCase
{
    use UserHelper;

    private const string USER_GROUP_NAME_FOR_TEST = 'Normal users';
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/user_group';
    private UserGroupResource $userGroupResource;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userGroupResource = static::getContainer()->get(UserGroupResource::class);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user_group` request returns `401` for non-logged user.')]
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
    #[DataProvider('dataProviderCreateUpdatePatchDeleteActions')]
    #[TestDox('Test that `$method $action` returns forbidden error for non-root user.')]
    public function testThatCreateUpdatePatchDeleteActionsForbiddenForNonRootUser(string $method, string $action): void
    {
        $this->checkIsForbidden('john-admin', 'password-admin', $method, $action);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderGetActions')]
    #[TestDox('Test that `$method $action` returns forbidden error for non-admin user.')]
    public function testThatGetActionsForbiddenForNonAdminUser(string $method, string $action): void
    {
        $this->checkIsForbidden('john-user', 'password-user', $method, $action);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user_group/count` for the admin user returns success response.')]
    public function testThatCountActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->countActionForAdminUserReturnsSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatFindActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/user_group` returns `$responseCode` with login:`$login`, password:`$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 4);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user_group/{id}` for the admin user returns success response.')]
    public function testThatFindOneActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $userGroupEntity = $this->userGroupResource->findOne(LoadUserGroupData::getUuidByKey('Role-logged'));
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);

        $client->request('GET', static::$baseUrl . '/' . $userGroupEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($userGroupEntity->getId(), $responseData['id']);
        self::assertEquals($userGroupEntity->getName(), $responseData['name']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user_group/ids` for the admin user returns success response.')]
    public function testThatIdsActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->idsActionForAdminUserReturnsSuccessResponse(4);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /api/v1/user_group` (create user group) for the root user returns success response.')]
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'name' => 'Test UserGroup controller',
            'role' => Role::LOGGED->value,
        ];
        $client->request(method: 'POST', uri: static::$baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($requestData['name'], $responseData['name']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /api/v1/user_group/{id}` for the root user returns success response.')]
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);
        $requestData = [
            'role' => Role::USER->value,
        ];

        $client->request(
            method: 'PATCH',
            uri: static::$baseUrl . '/' . $userGroupEntity->getId(),
            content: JSON::encode($requestData)
        );
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
    #[TestDox('Test that `PUT /api/v1/user_group/{id}` for the root user returns success response.')]
    public function testThatUpdateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);
        $requestData = [
            'name' => self::USER_GROUP_NAME_FOR_TEST . ' edited',
            'role' => Role::LOGGED->value,
        ];
        $client->request(
            method: 'PUT',
            uri: static::$baseUrl . '/' . $userGroupEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($requestData['name'], $responseData['name']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/user_group/{id}` under the non-root user returns error response.')]
    public function testThatDeleteActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);

        $client->request(method: 'DELETE', uri: static::$baseUrl . '/' . $userGroupEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);

        // let's check that row deleted inside db.
        /** @var UserGroup|null $userGroupEntity */
        $userGroupEntity = $this->userGroupResource->findOne($userGroupEntity->getId());
        self::assertNull($userGroupEntity);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderCreateUpdatePatchDeleteActions(): Generator
    {
        yield ['POST', static::$baseUrl];
        yield ['PUT', static::$baseUrl . '/' . LoadUserGroupData::getUuidByKey('Role-logged')];
        yield ['PATCH', static::$baseUrl . '/' . LoadUserGroupData::getUuidByKey('Role-logged')];
        yield ['DELETE', static::$baseUrl . '/' . LoadUserGroupData::getUuidByKey('Role-logged')];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderGetActions(): Generator
    {
        yield ['GET', static::$baseUrl . '/count'];
        yield ['GET', static::$baseUrl];
        yield ['GET', static::$baseUrl . '/' . LoadUserData::getUuidByKey('john-root')];
        yield ['GET', static::$baseUrl . '/ids'];
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public static function dataProviderTestThatFindActionWorksAsExpected(): Generator
    {
        // username === login
        yield ['john-api', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john', 'password', Response::HTTP_FORBIDDEN];
        yield ['john-logged', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john-user', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john-admin', 'password-admin', Response::HTTP_OK];
        yield ['john-root', 'password-root', Response::HTTP_OK];
    }

    /**
     * @param array<string, string> $responseData
     */
    protected function checkBasicFieldsInResponse(array $responseData): void
    {
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('name', $responseData);
    }

    /**
     * @throws Throwable
     */
    private function checkIsForbidden(string $username, string $password, string $method, string $action): void
    {
        $client = $this->getTestClient($username, $password);

        $client->request($method, $action);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }
}
