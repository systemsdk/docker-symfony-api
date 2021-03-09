<?php
declare(strict_types = 1);
/**
 * /tests/Functional/Controller/Api/HealthControllerTest.php
 */

namespace App\Tests\Functional\Controller\Api;

use App\Resource\LogRequestResource;
use App\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class HealthControllerTest
 *
 * @package App\Tests\Functional\Controller\Api
 */
class HealthControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/health';

    /**
     * @throws Throwable
     *
     * @testdox Test that health route returns success response
     */
    public function testThatHealthRouteReturns200(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     *
     * @testdox Test that health route does not make request log
     */
    public function testThatHealthRouteDoesNotMakeRequestLog(): void
    {
        static::bootKernel();

        /** @var LogRequestResource $resource */
        $resource = static::$container->get(LogRequestResource::class);
        $expectedLogCount = $resource->count();
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        static::assertSame($expectedLogCount, $resource->count());
    }
}
