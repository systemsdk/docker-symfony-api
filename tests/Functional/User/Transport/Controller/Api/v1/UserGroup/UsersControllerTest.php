<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\UserGroup;

use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Enum\Role;
use App\Tests\Functional\User\Transport\Controller\Api\v1\Traits\UserHelper;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class UsersControllerTest
 *
 * @package App\Tests
 */
class UsersControllerTest extends WebTestCase
{
    use UserHelper;

    private string $baseUrl = self::API_URL_PREFIX . '/v1/user_group';

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        static::assertInstanceOf(UserGroupResource::class, $userGroupResource);
        $userGroup = $userGroupResource->findOneBy([
            'role' => Role::ADMIN->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroup);
        $this->baseUrl .= '/' . $userGroup->getId() . '/users';
    }

    /**
     * @testdox Test that `GET /api/v1/user_group/{groupId}/users` request returns `401` for non-logged user.
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
     * @testdox Test that user group users returns `$responseCode` with login: `$login`, password: `$password`.
     *
     * @dataProvider dataProviderTestThatGetUserGroupUsersWorksAsExpected
     *
     * @throws Throwable
     */
    public function testThatGetUserGroupUsersWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $this->findActionWorksAsExpected($login, $password, $responseCode, 1);
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public function dataProviderTestThatGetUserGroupUsersWorksAsExpected(): Generator
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
