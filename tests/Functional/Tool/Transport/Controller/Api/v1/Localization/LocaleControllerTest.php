<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tool\Transport\Controller\Api\v1\Localization;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class LocaleControllerTest
 *
 * @package App\Tests
 */
class LocaleControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/localization/locale';
    private LocalizationServiceInterface $localizationService;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $localizationService = static::getContainer()->get(LocalizationServiceInterface::class);
        self::assertInstanceOf(LocalizationServiceInterface::class, $localizationService);
        $this->localizationService = $localizationService;
    }

    /**
     * @testdox Test that `GET /v1/localization/locale` returns success response.
     *
     * @throws Throwable
     */
    public function testThatGettingSupportedLocalesRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        $supportedLocales = array_flip($this->localizationService->getLocales());

        foreach ($responseData as $language) {
            self::assertArrayHasKey($language, $supportedLocales);
        }
    }
}
