<?php

declare(strict_types=1);

namespace App\Tests\Application\ApiKey\Transport\Controller\Api\V2\Traits;

use App\ApiKey\Domain\Entity\ApiKey;
use App\General\Domain\Utils\JSON;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
trait ApiKeyHelper
{
    /**
     * @throws Throwable
     */
    private function checkActionForNonRootUserReturnsForbiddenResponse(string $method, string $description): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $requestData = [
            'description' => $description,
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-api'),
            ],
        ];
        $this->wait();
        $client->request(
            method: $method,
            uri: $this->baseUrl . '/' . $this->apiKey->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);

        // let's check that after request row inside db wasn't updated.
        $this->checkThatApiKeyWasNotUpdated();
    }

    /**
     * @param array<string, string|array<string>> $requestData
     *
     * @throws Throwable
     */
    private function checkActionForRootUserWithWrongDataReturnsValidationErrorResponse(
        string $method,
        array $requestData,
        string $error
    ): void {
        $client = $this->getTestClient('john-root', 'password-root');

        $this->wait();
        $client->request(
            method: $method,
            uri: $this->baseUrl . '/' . $this->apiKey->getId(),
            content: JSON::encode($requestData)
        );

        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('message', $responseData);
        self::assertStringContainsString($error, $responseData['message']);

        // let's check that after request row inside db wasn't updated.
        $this->checkThatApiKeyWasNotUpdated();
    }

    /**
     * @throws Throwable
     */
    private function checkActionForRootUserReturnsSuccessResponse(string $method, string $description): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $requestData = [
            'description' => $description,
            'userGroups' => [
                LoadUserGroupData::getUuidByKey('Role-api'),
            ],
        ];
        $this->wait();
        $client->request(
            method: $method,
            uri: $this->baseUrl . '/' . $this->apiKey->getId(),
            content: JSON::encode($requestData)
        );
        $response = $client->getResponse();
        $responseContent = $response->getContent();
        self::assertNotFalse($responseContent);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($responseContent, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('description', $responseData);
        self::assertEquals($requestData['description'], $responseData['description']);

        // let's check that after request row inside db was updated.
        $this->checkThatApiKeyWasUpdated();
    }

    /**
     * @throws Throwable
     */
    private function checkThatApiKeyWasNotUpdated(): void
    {
        $apiKey = $this->getApiKey();
        self::assertEquals($this->apiKey->getUpdatedAt()?->getTimestamp(), $apiKey->getUpdatedAt()?->getTimestamp());
    }

    /**
     * @throws Throwable
     */
    private function checkThatApiKeyWasUpdated(): void
    {
        $apiKey = $this->getApiKey();
        self::assertGreaterThan(
            $this->apiKey->getUpdatedAt()?->getTimestamp(),
            $apiKey->getUpdatedAt()?->getTimestamp()
        );
    }

    /**
     * @throws Throwable
     */
    private function getApiKey(): ApiKey
    {
        $apiKey = $this->apiKeyFindOneResource->findOne($this->apiKey->getId());
        self::assertInstanceOf(ApiKey::class, $apiKey);

        return $apiKey;
    }

    private function wait(): void
    {
        // let's wait 1 second due to sometimes it can be fast and time before and after update the same in seconds.
        sleep(1);
    }
}
