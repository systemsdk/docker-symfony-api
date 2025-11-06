<?php

declare(strict_types=1);

namespace App\Tests\Application\Tool\Transport\Controller\Api\V1\Localization;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class TimeZoneControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/localization/timezone';
    private LocalizationServiceInterface $localizationService;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->localizationService = static::getContainer()->get(LocalizationServiceInterface::class);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /v1/localization/timezone` returns success response.')]
    public function testThatGettingSupportedTimeZonesRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        $supportedTimeZones = $this->localizationService->getTimezones();
        self::assertCount(count($supportedTimeZones), $responseData);

        // let's check the structure for the first time zone
        self::assertArrayHasKey(0, $responseData);
        self::assertIsArray($responseData[0]);
        self::assertArrayHasKey('timezone', $responseData[0]);
        self::assertArrayHasKey('identifier', $responseData[0]);
        self::assertArrayHasKey('offset', $responseData[0]);
        self::assertArrayHasKey('value', $responseData[0]);
    }
}
