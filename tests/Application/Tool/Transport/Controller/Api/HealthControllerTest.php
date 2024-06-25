<?php

declare(strict_types=1);

namespace App\Tests\Application\Tool\Transport\Controller\Api;

use App\Log\Application\Resource\LogRequestResource;
use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class HealthControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/health';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that health route returns success response.')]
    public function testThatHealthRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that health route does not make request log.')]
    public function testThatHealthRouteDoesNotMakeRequestLog(): void
    {
        $resource = static::getContainer()->get(LogRequestResource::class);
        $expectedLogCount = $resource->count();
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        static::assertSame($expectedLogCount, $resource->count());
    }
}
