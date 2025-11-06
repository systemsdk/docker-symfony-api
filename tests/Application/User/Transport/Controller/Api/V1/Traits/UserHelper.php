<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Traits;

use App\General\Domain\Utils\JSON;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
trait UserHelper
{
    /**
     * @param array<string, string> $responseData
     */
    abstract protected function checkBasicFieldsInResponse(array $responseData): void;

    /**
     * @throws Throwable
     */
    private function countActionForAdminUserReturnsSuccessResponse(): void
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
    private function findActionWorksAsExpected(
        string $login,
        string $password,
        int $responseCode,
        int $expectedCount
    ): void {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', static::$baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode(), "Response:\n" . $response);

        if ($responseCode === Response::HTTP_OK) {
            $responseData = JSON::decode($content, true);
            self::assertIsArray($responseData);
            self::assertGreaterThan($expectedCount, count($responseData));
            self::assertIsArray($responseData[0]);
            $this->checkBasicFieldsInResponse($responseData[0]);
        }
    }

    /**
     * @throws Throwable
     */
    private function idsActionForAdminUserReturnsSuccessResponse(int $expectedCount): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', static::$baseUrl . '/ids');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertGreaterThan($expectedCount, count($responseData));
        self::assertIsString($responseData[0]);
    }
}
