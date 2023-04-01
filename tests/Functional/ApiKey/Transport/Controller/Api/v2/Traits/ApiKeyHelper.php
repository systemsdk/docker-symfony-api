<?php

declare(strict_types=1);

namespace App\Tests\Functional\ApiKey\Transport\Controller\Api\v2\Traits;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate;
use App\ApiKey\Application\Resource\ApiKeyCreateResource;
use App\ApiKey\Domain\Entity\ApiKey;
use App\General\Domain\Utils\JSON;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait ApiKeyHelper
 *
 * @package App\Tests
 */
trait ApiKeyHelper
{
    /**
     * @throws Throwable
     */
    private function findOrCreateApiKey(): ApiKey
    {
        /** @var ApiKey|null $apiKey */
        $apiKey = $this->apiKeyFindOneResource->findOneBy([
            'description' => 'test api key',
        ]);

        if (!$apiKey) {
            /** @var ApiKeyCreateResource $apiKeyCreateResource */
            $apiKeyCreateResource = static::getContainer()->get(ApiKeyCreateResource::class);
            /** @var UserGroupResource $userGroupResource */
            $userGroupResource = static::getContainer()->get(UserGroupResource::class);
            /** @var UserGroup|null $userGroup */
            $userGroup = $userGroupResource->findOne(LoadUserGroupData::$uuids['Role-api']);
            self::assertInstanceOf(UserGroup::class, $userGroup);
            $dto = (new ApiKeyCreate())->setDescription('test api key')->setUserGroups([$userGroup]);
            $apiKey = $apiKeyCreateResource->create($dto, true);
        }

        return $apiKey;
    }

    /**
     * @throws Throwable
     */
    private function checkActionForNonRootUserReturnsForbiddenResponse(string $method, string $description): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $requestData = [
            'description' => $description,
            'userGroups' => [
                LoadUserGroupData::$uuids['Role-api'],
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
                LoadUserGroupData::$uuids['Role-api'],
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
