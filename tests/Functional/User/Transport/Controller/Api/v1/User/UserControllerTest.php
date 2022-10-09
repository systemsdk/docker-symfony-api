<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\User;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Tests\Functional\User\Transport\Controller\Api\v1\Traits\UserHelper;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserData;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class UserControllerTest
 *
 * @package App\Tests
 */
class UserControllerTest extends WebTestCase
{
    use UserHelper;

    private const USERNAME_FOR_TEST = 'test-user-controller';
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private UserResource $userResource;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userResource = static::getContainer()->get(UserResource::class);
        static::assertInstanceOf(UserResource::class, $userResource);
        $this->userResource = $userResource;
    }

    /**
     * @testdox Test that `GET /api/v1/user` request returns `401` for non-logged user.
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
     * @dataProvider dataProviderCreateUpdatePatchActions
     *
     * @throws Throwable
     */
    public function testThatCreateUpdatePatchActionsForbiddenForNonRootUser(string $method, string $action): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request($method, $action);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
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
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request($method, $action);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `GET /api/v1/user/count` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatCountActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->countActionForAdminUserReturnsSuccessResponse();
    }

    /**
     * @testdox Test that `GET /api/v1/user` returns `$responseCode` with login: `$login`, password: `$password`.
     *
     * @dataProvider dataProviderTestThatFindActionWorksAsExpected
     *
     * @throws Throwable
     */
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 5);
    }

    /**
     * @testdox Test that `GET /api/v1/user/{id}` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatFindOneActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $userEntity = $this->userResource->findOneBy([
            'username' => 'john',
        ]);
        self::assertInstanceOf(User::class, $userEntity);

        $client->request('GET', $this->baseUrl . '/' . $userEntity->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        self::assertEquals($userEntity->getUsername(), $responseData['username']);
        self::assertEquals($userEntity->getFirstName(), $responseData['firstName']);
        self::assertEquals($userEntity->getLastName(), $responseData['lastName']);
        self::assertEquals($userEntity->getEmail(), $responseData['email']);
        self::assertEquals($userEntity->getLanguage(), $responseData['language']);
        self::assertEquals($userEntity->getLocale(), $responseData['locale']);
        self::assertEquals($userEntity->getTimezone(), $responseData['timezone']);
    }

    /**
     * @testdox Test that `GET /api/v1/user/ids` for the admin user returns success response.
     *
     * @throws Throwable
     */
    public function testThatIdsActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->idsActionForAdminUserReturnsSuccessResponse(5);
    }

    /**
     * @testdox Test that `POST /api/v1/user` (create user) for the root user returns success response.
     *
     * @throws Throwable
     */
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'username' => self::USERNAME_FOR_TEST,
            'firstName' => 'Name',
            'lastName' => 'Last name',
            'email' => self::USERNAME_FOR_TEST . '@test.com',
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-logged'],
            ],
            'password' => 'test12345',
            'language' => LocalizationServiceInterface::DEFAULT_LANGUAGE,
            'locale' => LocalizationServiceInterface::DEFAULT_LOCALE,
            'timezone' => LocalizationServiceInterface::DEFAULT_TIMEZONE,
        ];
        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        $this->checkThatRequestEqualsResponseData($requestData, $responseData);
    }

    /**
     * @testdox Test that `PATCH /api/v1/user/{id}` for the root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userEntity = $this->userResource->findOneBy([
            'username' => self::USERNAME_FOR_TEST,
        ]);
        self::assertInstanceOf(User::class, $userEntity);
        $requestData = [
            'password' => 'test12345',
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-user'],
            ],
        ];

        $client->request(
            method: 'PATCH',
            uri: $this->baseUrl . '/' . $userEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);

        // let's check that after patch we have 2 user groups
        $user = $this->userResource->findOne($responseData['id']);
        self::assertInstanceOf(User::class, $user);
        $userGroups = $user->getUserGroups()->toArray();
        self::assertCount(2, $userGroups);

        foreach ($userGroups as $userGroup) {
            self::assertContains(
                $userGroup->getId(),
                [LoadUserGroupData::$uuids['Role-logged'], LoadUserGroupData::$uuids['Role-user']]
            );
        }
    }

    /**
     * @testdox Test that `PUT /api/v1/user/{id}` for the root user returns success response.
     *
     * @depends testThatCreateActionForRootUserReturnsSuccessResponse
     *
     * @throws Throwable
     */
    public function testThatUpdateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $userEntity = $this->userResource->findOneBy([
            'username' => self::USERNAME_FOR_TEST,
        ]);
        self::assertInstanceOf(User::class, $userEntity);
        $requestData = [
            'username' => self::USERNAME_FOR_TEST,
            'firstName' => 'Name edited',
            'lastName' => 'Last name edited',
            'email' => self::USERNAME_FOR_TEST . '@test1.com',
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-logged'],
            ],
            'password' => 'test123456',
            'language' => LocalizationServiceInterface::DEFAULT_LANGUAGE,
            'locale' => LocalizationServiceInterface::DEFAULT_LOCALE,
            'timezone' => LocalizationServiceInterface::DEFAULT_TIMEZONE,
        ];
        $client->request(
            method: 'PUT',
            uri: $this->baseUrl . '/' . $userEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        $this->checkThatRequestEqualsResponseData($requestData, $responseData);

        // let's delete our test user for cleanup user group users
        $this->userResource->delete($responseData['id']);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderCreateUpdatePatchActions(): Generator
    {
        yield ['POST', $this->baseUrl];
        yield ['PUT', $this->baseUrl . '/' . LoadUserData::$uuids['john-root']];
        yield ['PATCH', $this->baseUrl . '/' . LoadUserData::$uuids['john-root']];
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
        yield ['john', 'password', Response::HTTP_FORBIDDEN];
        yield ['john-logged', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john-api', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john-user', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john-admin', 'password-admin', Response::HTTP_OK];
        yield ['john-root', 'password-root', Response::HTTP_OK];

        // email === login
        yield ['john.doe@test.com', 'password', Response::HTTP_FORBIDDEN];
        yield ['john.doe-logged@test.com', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john.doe-api@test.com', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john.doe-user@test.com', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john.doe-admin@test.com', 'password-admin', Response::HTTP_OK];
        yield ['john.doe-root@test.com', 'password-root', Response::HTTP_OK];
    }

    /**
     * @param array<string, string> $responseData
     */
    protected function checkBasicFieldsInResponse(array $responseData): void
    {
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('username', $responseData);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertArrayHasKey('email', $responseData);
        self::assertArrayHasKey('language', $responseData);
        self::assertArrayHasKey('locale', $responseData);
        self::assertArrayHasKey('timezone', $responseData);
    }

    /**
     * @param array<string, string|string[]> $requestData
     * @param array<string, string> $responseData
     *
     * @throws Throwable
     */
    private function checkThatRequestEqualsResponseData(array $requestData, array $responseData): void
    {
        self::assertEquals($requestData['username'], $responseData['username']);
        self::assertEquals($requestData['firstName'], $responseData['firstName']);
        self::assertEquals($requestData['lastName'], $responseData['lastName']);
        self::assertEquals($requestData['email'], $responseData['email']);
        self::assertEquals($requestData['language'], $responseData['language']);
        self::assertEquals($requestData['locale'], $responseData['locale']);
        self::assertEquals($requestData['timezone'], $responseData['timezone']);

        // let's check saved user groups
        self::assertIsString($responseData['id']);
        $user = $this->userResource->findOne($responseData['id']);
        self::assertInstanceOf(User::class, $user);
        self::assertCount(1, $user->getUserGroups());
        /** @var UserGroup|false $userGroup */
        $userGroup = $user->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $userGroup);
        self::assertEquals(LoadUserGroupData::$uuids['Role-logged'], $userGroup->getId());
    }
}
