<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\User;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Domain\Utils\JSON;
use App\Role\Domain\Enum\Role;
use App\Tests\Application\User\Transport\Controller\Api\V1\Traits\UserHelper;
use App\Tests\TestCase\WebTestCase;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
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
class UserControllerTest extends WebTestCase
{
    use UserHelper;

    private const string USERNAME_FOR_TEST = 'john-logged';
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private UserResource $userResource;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userResource = static::getContainer()->get(UserResource::class);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user` request returns `401` for non-logged user.')]
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
    #[DataProvider('dataProviderCreateUpdatePatchActions')]
    #[TestDox('Test that `$method $action` returns forbidden error for non-root user.')]
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
     * @throws Throwable
     */
    #[DataProvider('dataProviderGetActions')]
    #[TestDox('Test that `$method $action` returns forbidden error for non-admin user.')]
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
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/count` for the admin user returns success response.')]
    public function testThatCountActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->countActionForAdminUserReturnsSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/count` for the api key with admin role returns success response.')]
    public function testThatCountActionForApiKeyReturnsSuccessResponse(): void
    {
        $client = $this->getApiKeyClient(Role::ADMIN->value);

        $client->request(method: 'GET', uri: static::$baseUrl . '/count');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('count', $responseData);
        self::assertGreaterThan(0, $responseData['count']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/count` for the wrong api key returns error response.')]
    public function testThatCountActionForWrongApiKeyReturnsErrorResponse(): void
    {
        $client = $this->getApiKeyClient();

        $client->request(method: 'GET', uri: static::$baseUrl . '/count');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertStringContainsString('Invalid ApiKey', $responseData['message']);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatFindActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/user` returns `$responseCode` with login: `$login`, password: `$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 5);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/{id}` for the admin user returns success response.')]
    public function testThatFindOneActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $userEntity = $this->userResource->findOneBy([
            'username' => 'john',
        ]);
        self::assertInstanceOf(User::class, $userEntity);

        $client->request('GET', static::$baseUrl . '/' . $userEntity->getId());
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
        self::assertEquals($userEntity->getLanguage()->value, $responseData['language']);
        self::assertEquals($userEntity->getLocale()->value, $responseData['locale']);
        self::assertEquals($userEntity->getTimezone(), $responseData['timezone']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/ids` for the admin user returns success response.')]
    public function testThatIdsActionForAdminUserReturnsSuccessResponse(): void
    {
        $this->idsActionForAdminUserReturnsSuccessResponse(5);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /api/v1/user` (create user) for the root user returns success response.')]
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'username' => 'test-user-controller',
            'firstName' => 'Name',
            'lastName' => 'Last name',
            'email' => 'test-user-controller@test.com',
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-logged'),
            ],
            'password' => 'test12345',
            'language' => Language::getDefault()->value,
            'locale' => Locale::getDefault()->value,
            'timezone' => LocalizationServiceInterface::DEFAULT_TIMEZONE,
        ];
        $client->request(method: 'POST', uri: static::$baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        $this->checkThatRequestEqualsResponseData($requestData, $responseData);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /api/v1/user/{id}` for the root user returns success response.')]
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
                LoadUserGroupData::getUuidByKey('Role-user'),
            ],
        ];

        $client->request(
            method: 'PATCH',
            uri: static::$baseUrl . '/' . $userEntity->getId(),
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
                [LoadUserGroupData::getUuidByKey('Role-logged'), LoadUserGroupData::getUuidByKey('Role-user')]
            );
        }
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PUT /api/v1/user/{id}` for the root user returns success response.')]
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
                LoadUserGroupData::getUuidByKey('Role-logged'),
            ],
            'password' => 'test123456',
            'language' => Language::getDefault()->value,
            'locale' => Locale::getDefault()->value,
            'timezone' => LocalizationServiceInterface::DEFAULT_TIMEZONE,
        ];
        $client->request(
            method: 'PUT',
            uri: static::$baseUrl . '/' . $userEntity->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        $this->checkBasicFieldsInResponse($responseData);
        $this->checkThatRequestEqualsResponseData($requestData, $responseData);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderCreateUpdatePatchActions(): Generator
    {
        yield ['POST', static::$baseUrl];
        yield ['PUT', static::$baseUrl . '/' . LoadUserData::getUuidByKey('john-root')];
        yield ['PATCH', static::$baseUrl . '/' . LoadUserData::getUuidByKey('john-root')];
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
        $user = $this->userResource->findOne($responseData['id']);
        self::assertInstanceOf(User::class, $user);
        self::assertCount(1, $user->getUserGroups());
        /** @var UserGroup|false $userGroup */
        $userGroup = $user->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $userGroup);
        self::assertEquals(LoadUserGroupData::getUuidByKey('Role-logged'), $userGroup->getId());
    }
}
