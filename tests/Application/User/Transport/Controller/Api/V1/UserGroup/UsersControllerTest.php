<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\UserGroup;

use App\Role\Domain\Enum\Role;
use App\Tests\Application\User\Transport\Controller\Api\V1\Traits\UserHelper;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class UsersControllerTest extends WebTestCase
{
    use UserHelper;

    protected static string $baseUrl;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        $userGroup = $userGroupResource->findOneBy([
            'role' => Role::ADMIN->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroup);
        static::$baseUrl = self::API_URL_PREFIX . '/v1/user_group/' . $userGroup->getId() . '/users';
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user_group/{groupId}/users` request returns `401` for non-logged user.')]
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
    #[DataProvider('dataProviderTestThatGetUserGroupUsersWorksAsExpected')]
    #[TestDox('Test that user group users returns `$responseCode` with login: `$login`, password: `$password`.')]
    public function testThatGetUserGroupUsersWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 1);
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public static function dataProviderTestThatGetUserGroupUsersWorksAsExpected(): Generator
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
        self::assertArrayHasKey('username', $responseData);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertArrayHasKey('email', $responseData);
        self::assertArrayHasKey('language', $responseData);
        self::assertArrayHasKey('locale', $responseData);
        self::assertArrayHasKey('timezone', $responseData);
    }
}
