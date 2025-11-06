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
class LanguageControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/localization/language';
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
    #[TestDox('Test that `GET /v1/localization/language` returns success response.')]
    public function testThatGettingSupportedLanguagesRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        $supportedLanguages = array_flip($this->localizationService->getLanguages());

        foreach ($responseData as $language) {
            self::assertArrayHasKey($language, $supportedLanguages);
        }
    }
}
