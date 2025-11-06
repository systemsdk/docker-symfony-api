<?php

declare(strict_types=1);

namespace App\Tests\Application\Role\Transport\Controller\Api\V1\Role;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class RoleControllerTest extends WebTestCase
{
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/role';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/role` request returns `401` for non-logged user.')]
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
    #[TestDox('Test that `GET /api/v1/role/count` for the admin user returns success response.')]
    public function testThatCountActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

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
    #[DataProvider('dataProviderTestThatFindActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/role` returns `$responseCode` with login: `$login`, password: `$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', static::$baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode(), "Response:\n" . $response);

        if ($responseCode === Response::HTTP_OK) {
            $responseData = JSON::decode($content, true);
            self::assertIsArray($responseData);
            self::assertGreaterThan(4, count($responseData));
            self::assertIsArray($responseData[0]);
            $this->checkBasicFieldsInResponse($responseData[0]);
        }
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/role/ids` for the admin user returns success response.')]
    public function testThatIdsActionForAdminUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', static::$baseUrl . '/ids');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertGreaterThan(4, count($responseData));
        self::assertIsString($responseData[0]);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderGetActions(): Generator
    {
        yield ['GET', static::$baseUrl . '/count'];
        yield ['GET', static::$baseUrl];
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
    }

    /**
     * @param array<string, string> $responseData
     */
    private function checkBasicFieldsInResponse(array $responseData): void
    {
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('description', $responseData);
    }
}
