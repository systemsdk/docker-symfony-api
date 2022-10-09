<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\UserGroup;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Entity\Role;
use App\Tests\Functional\User\Transport\Controller\Api\v1\Traits\UserHelper;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserData;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class UserGroupControllerTest
 *
 * @package App\Tests
 */
class UserGroupControllerTest extends WebTestCase
{
    use UserHelper;

    private const USER_GROUP_NAME_FOR_TEST = 'Test UserGroup controller';
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user_group';
    private UserGroupResource $userGroupResource;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        static::assertInstanceOf(UserGroupResource::class, $userGroupResource);
        $this->userGroupResource = $userGroupResource;
    }

    /**
     * @testdox Test that `GET /api/v1/user_group` request returns `401` for non-logged user.
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
     * @dataProvider dataProviderCreateUpdatePatchDeleteActions
     *
     * @throws Throwable
     */
    public function testThatCreateUpdatePatchDeleteActionsForbiddenForNonRootUser(string $method, string $action): void
    {
        $this->checkIsForbidden('john-admin', 'password-admin', $method, $action);
    }

    /**
     * @testdox Test that `$method $action` returns forbidden error for non-admin user.
     *
     * @dataProvider dataProviderGetActions
     *
     * @throws Throwable
     */
    public function testThatGetActionsForbiddenForNonAdminUser(string $method, string $action): void
    {
        $this->checkIsForbidden('john-user', 'password-user', $method, $action);
    }

    /**
     * @testdox Test that `GET /api/v1/user_group/count` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatCountActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->countActionForAdminUserReturnsSuccessResponse();
    }

    /**
     * @testdox Test that `GET /api/v1/user_group` returns `$responseCode` with login: `$login`, password: `$password`.
     *
     * @dataProvider dataProviderTestThatFindActionWorksAsExpected
     *
     * @throws Throwable
     */
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 4);
    }

    /**
     * @testdox Test that `GET /api/v1/user_group/{id}` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatFindOneActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $userGroupEntity = $this->userGroupResource->findOne(LoadUserGroupData::$uuids['Role-logged']);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);

        $client->request('GET', $this->baseUrl . '/' . $userGroupEntity->getId());
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
     * @testdox Test that `GET /api/v1/user_group/ids` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatIdsActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->idsActionForAdminUserReturnsSuccessResponse(4);
    }

    /**
     * @testdox Test that `POST /api/v1/user_group` (create user group) for the root user returns success response.
     *
     * @throws Throwable
     */
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'name' => self::USER_GROUP_NAME_FOR_TEST,
            'role' => Role::ROLE_LOGGED,
        ];
        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals(self::USER_GROUP_NAME_FOR_TEST, $responseData['name']);
    }

    /**
     * @testdox Test that `PATCH /api/v1/user_group/{id}` for the root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);
        $requestData = [
            'role' => Role::ROLE_USER,
        ];

        $client->request(
            method: 'PATCH',
            uri: $this->baseUrl . '/' . $userGroupEntity->getId(),
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
     * @testdox Test that `PUT /api/v1/user_group/{id}` for the root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatUpdateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);
        $requestData = [
            'name' => self::USER_GROUP_NAME_FOR_TEST . ' edited',
            'role' => Role::ROLE_LOGGED,
        ];
        $client->request(
            method: 'PUT',
            uri: $this->baseUrl . '/' . $userGroupEntity->getId(),
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
     * @testdox Test that `DELETE /api/v1/user_group/{id}` under the non-root user returns error response.
     *
     * @depends testThatUpdateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatDeleteActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userGroupEntity = $this->userGroupResource->findOneBy([
            'name' => self::USER_GROUP_NAME_FOR_TEST . ' edited',
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupEntity);

        $client->request(method: 'DELETE', uri: $this->baseUrl . '/' . $userGroupEntity->getId());
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
    public function dataProviderCreateUpdatePatchDeleteActions(): Generator
    {
        yield ['POST', $this->baseUrl];
        yield ['PUT', $this->baseUrl . '/' . LoadUserGroupData::$uuids['Role-logged']];
        yield ['PATCH', $this->baseUrl . '/' . LoadUserGroupData::$uuids['Role-logged']];
        yield ['DELETE', $this->baseUrl . '/' . LoadUserGroupData::$uuids['Role-logged']];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderGetActions(): Generator
    {
        yield ['GET', $this->baseUrl . '/count'];
        yield ['GET', $this->baseUrl];
        yield ['GET', $this->baseUrl . '/' . LoadUserData::$uuids['john-root']];
        yield ['GET', $this->baseUrl . '/ids'];
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public function dataProviderTestThatFindActionWorksAsExpected(): Generator
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
