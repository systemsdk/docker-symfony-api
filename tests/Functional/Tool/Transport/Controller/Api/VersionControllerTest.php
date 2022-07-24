<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tool\Transport\Controller\Api;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Log\Application\Resource\LogRequestResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function file_get_contents;

/**
 * Class VersionControllerTest
 *
 * @package App\Tests
 */
class VersionControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/version';

    /**
     * @testdox Test that version route returns success response.
     *
     * @throws Throwable
     */
    public function testThatVersionRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @testdox Test that version route does not make request log.
     *
     * @throws Throwable
     */
    public function testThatVersionRouteDoesNotMakeRequestLog(): void
    {
        $resource = static::getContainer()->get(LogRequestResource::class);
        static::assertInstanceOf(LogRequestResource::class, $resource);
        $expectedLogCount = $resource->count();
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        static::assertSame($expectedLogCount, $resource->count());
    }

    /**
     * @testdox Test that 'X-API-VERSION' is added to response headers.
     *
     * @throws Throwable
     */
    public function testThatApiVersionIsAddedToResponseHeaders(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $version = $response->headers->get('X-API-VERSION');
        static::assertNotNull($version);
        static::assertSame(
            JSON::decode((string)file_get_contents(__DIR__ . '/../../../../../../composer.json'))->version,
            $version,
        );
    }
}
