<?php

declare(strict_types=1);

namespace App\Tests\Application\Role\Transport\Controller\Api\V1\Role;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Resource\RoleResource;
use App\Role\Domain\Entity\Role as RoleEntity;
use App\Role\Domain\Enum\Role;
use App\Tests\TestCase\WebTestCase;
use Exception;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class FindOneRoleControllerTest extends WebTestCase
{
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/role';
    private readonly RoleResource $roleResource;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->roleResource = static::getContainer()->get(RoleResource::class);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/role/{role}` request returns `401` for non-logged user.')]
    public function testThatGetBaseRouteReturn401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', static::$baseUrl . '/' . Role::LOGGED->value);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/role/{role}` returns `$responseCode` with login: `$login`, '
        . 'password: `$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', static::$baseUrl . '/' . Role::ROOT->value);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode());

        if ($responseCode === Response::HTTP_OK) {
            $responseData = JSON::decode($content, true);
            self::assertIsArray($responseData);
            $this->checkResponse($responseData);
        }
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public static function dataProviderTestThatActionWorksAsExpected(): Generator
    {
        yield ['john', 'password', Response::HTTP_FORBIDDEN];
        yield ['john-logged', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john-api', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john-user', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john-admin', 'password-admin', Response::HTTP_OK];
        yield ['john-root', 'password-root', Response::HTTP_OK];
    }

    /**
     * @param array<string, string> $responseData
     *
     * @throws Throwable
     */
    private function checkResponse(array $responseData): void
    {
        $role = $this->roleResource->findOne(Role::ROOT->value);
        self::assertInstanceOf(RoleEntity::class, $role);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($role->getId(), $responseData['id']);
        self::assertEquals($role->getDescription(), $responseData['description']);
    }
}
