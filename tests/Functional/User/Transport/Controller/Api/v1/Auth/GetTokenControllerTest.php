<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\Auth;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use Generator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class GetTokenControllerTest
 *
 * @package App\Tests
 */
class GetTokenControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/auth';

    /**
     * @testdox Test that `POST /api/v1/auth/get_token` for the `$username` returns success response.
     *
     * @dataProvider dataProviderUsers
     *
     * @throws Throwable
     */
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
     * @testdox Test that `POST /api/v1/auth/get_token` with wrong password returns error response.
     *
     * @throws Throwable
     */
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
    public function dataProviderUsers(): Generator
    {
        yield ['john', 'password'];
        yield ['john-logged', 'password-logged'];
        yield ['john-api', 'password-api'];
        yield ['john-user', 'password-user'];
        yield ['john-admin', 'password-admin'];
        yield ['john-root', 'password-root'];
    }
}
