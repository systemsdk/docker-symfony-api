<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Resource\LogRequestResource;
use App\Utils\JSON;
use App\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function file_get_contents;

/**
 * Class VersionControllerTest
 *
 * @package App\Tests\Functional\Controller\Api
 */
class VersionControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/version';

    /**
     * @throws Throwable
     *
     * @testdox Test that version route returns success response
     */
    public function testThatVersionRouteReturns200(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     *
     * @testdox Test that version route does not make request log
     */
    public function testThatVersionRouteDoesNotMakeRequestLog(): void
    {
        /** @var LogRequestResource $resource */
        $resource = static::getContainer()->get(LogRequestResource::class);
        $expectedLogCount = $resource->count();
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        static::assertSame($expectedLogCount, $resource->count());
    }

    /**
     * @throws Throwable
     *
     * @testdox Test that 'X-API-VERSION' is added to response headers
     */
    public function testThatApiVersionIsAddedToResponseHeaders(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        $response = $client->getResponse();
        $version = $response->headers->get('X-API-VERSION');
        static::assertNotNull($version);
        static::assertSame(
            JSON::decode((string)file_get_contents(__DIR__ . '/../../../../composer.json'))->version,
            $version,
        );
    }
}
