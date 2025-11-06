<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Auth;

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
class GetTokenControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/auth';

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderUsers')]
    #[TestDox('Test that `POST /api/v1/auth/get_token` for the `$username` returns success response.')]
    public function testThatGetTokenActionForUsersReturnsSuccessResponse(string $username, string $password): void
    {
        $client = $this->getTestClient();

        $requestData = [
            'username' => $username,
            'password' => $password,
        ];
        $client->request(method: 'POST', uri: $this->baseUrl . '/get_token', content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('token', $responseData);
        self::assertIsString($responseData['token']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /api/v1/auth/get_token` with wrong password returns error response.')]
    public function testThatGetTokenActionForUserWithWrongPasswordReturnsErrorResponse(): void
    {
        $client = $this->getTestClient();

        $requestData = [
            'username' => 'john',
            'password' => 'wrong-password',
        ];
        $client->request(method: 'POST', uri: $this->baseUrl . '/get_token', content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('code', $responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $responseData['code']);
        self::assertEquals('Invalid credentials.', $responseData['message']);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function dataProviderUsers(): Generator
    {
        yield ['john', 'password'];
        yield ['john-logged', 'password-logged'];
        yield ['john-api', 'password-api'];
        yield ['john-user', 'password-user'];
        yield ['john-admin', 'password-admin'];
        yield ['john-root', 'password-root'];
    }
}
