<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\User;

use App\General\Domain\Utils\JSON;
use App\Role\Domain\Enum\Role;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
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
        $userEntity = $userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $userEntity);
        $this->userEntity = $userEntity;
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/{$userId}/roles` request returns `401` for non-logged user.')]
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
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/{$userId}/roles` returns forbidden error for non-root/non-himself users.')]
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
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatGetUserRolesWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/user/{$userId}/roles` success under root/himself users.')]
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
            self::assertContains($role, [Role::LOGGED->value, Role::USER->value]);
        }
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderTestThatGetUserRolesWorksAsExpected(): Generator
    {
        yield ['john-user', 'password-user'];
        yield ['john-root', 'password-root'];
    }
}
