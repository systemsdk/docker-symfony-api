<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\User;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Entity\Role;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class UserRolesControllerTest
 *
 * @package App\Tests
 */
class UserRolesControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private User $userEntity;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userResource = static::getContainer()->get(UserResource::class);
        static::assertInstanceOf(UserResource::class, $userResource);
        $userEntity = $userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $userEntity);
        $this->userEntity = $userEntity;
    }

    /**
     * @testdox Test that `GET /api/v1/user/{$userId}/roles` request returns `401` for non-logged user.
     *
     * @throws Throwable
     */
    public function testThatGetUserRolesForNonLoggedUserReturns401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl . '/' . $this->userEntity->getId() . '/roles');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `GET /api/v1/user/{$userId}/roles` returns forbidden error for non-root/non-himself users.
     *
     * @throws Throwable
     */
    public function testThatGetUserRolesForbiddenForNonRootAndNonHimselfUsers(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', $this->baseUrl . '/' . $this->userEntity->getId() . '/roles');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that `GET /api/v1/user/{$userId}/roles` success under root/himself users.
     *
     * @dataProvider dataProviderTestThatGetUserRolesWorksAsExpected
     *
     * @throws Throwable
     */
    public function testThatGetUserRolesWorksAsExpected(string $login, string $password): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', $this->baseUrl . '/' . $this->userEntity->getId() . '/roles');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertCount(2, $responseData);
        foreach ($responseData as $role) {
            self::assertContains($role, [Role::ROLE_LOGGED, Role::ROLE_USER]);
        }
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderTestThatGetUserRolesWorksAsExpected(): Generator
    {
        yield ['john-user', 'password-user'];
        yield ['john-root', 'password-root'];
    }
}
