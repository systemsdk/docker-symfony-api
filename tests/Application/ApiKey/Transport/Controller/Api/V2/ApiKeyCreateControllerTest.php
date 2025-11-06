<?php

declare(strict_types=1);

namespace App\Tests\Application\ApiKey\Transport\Controller\Api\V2;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class ApiKeyCreateControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v2/api_key';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /v2/api_key` returns forbidden error for non-root user.')]
    public function testThatCreateActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $requestData = [
            'description' => 'test api key',
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-api'),
            ],
        ];
        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @param array<string, string|array<string>> $requestData
     *
     * @throws Throwable
     */
    #[DataProvider('dataProviderWithIncorrectData')]
    #[TestDox('Test that `POST /v2/api_key` with wrong data returns validation error.')]
    public function testThatCreateActionForRootUserWithWrongDataReturnsValidationErrorResponse(
        array $requestData,
        string $error
    ): void {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('message', $responseData);
        self::assertStringContainsString($error, $responseData['message']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /v2/api_key` for the Root user returns success response.')]
    public function testThatCreateActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'description' => 'test api key',
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-api'),
            ],
        ];
        $client->request(method: 'POST', uri: $this->baseUrl, content: JSON::encode($requestData));
        $response = $client->getResponse();
        $responseContent = $response->getContent();
        self::assertNotFalse($responseContent);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($responseContent, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($requestData['description'], $responseData['description']);
    }

    /**
     * @return Generator<array{0: array<string, string|array<string>>, 1: string}>
     */
    public static function dataProviderWithIncorrectData(): Generator
    {
        yield [
            [
                'description' => '',
                'userGroups' => [
                    LoadUserGroupData::getUuidByKey('Role-api'),
                ],
            ],
            'This value should not be blank.',
        ];
        yield [
            [
                'description' => 'test api key',
                'userGroups' => [
                    '90000000-0000-1000-8000-900000000009',
                ],
            ],
            'id(90000000-0000-1000-8000-900000000009) was not found',
        ];
    }
}
