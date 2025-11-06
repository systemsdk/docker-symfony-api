<?php

declare(strict_types=1);

namespace App\Tests\Application\ApiKey\Transport\Controller\Api\V2;

use App\ApiKey\Infrastructure\DataFixtures\ORM\LoadApiKeyData;
use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class ApiKeyViewControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v2/api_key';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /v2/api_key/{id}` returns forbidden error for non-root user.')]
    public function testThatFindOneActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', $this->baseUrl . '/' . LoadApiKeyData::getUuidByKey('-logged'));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /v2/api_key/{id}` for the Root user returns success response.')]
    public function testThatFindOneActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('GET', $this->baseUrl . '/' . LoadApiKeyData::getUuidByKey('-logged'));
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals(LoadApiKeyData::getUuidByKey('-logged'), $responseData['id']);
    }
}
