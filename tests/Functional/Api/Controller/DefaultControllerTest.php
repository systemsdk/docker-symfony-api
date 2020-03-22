<?php
declare(strict_types = 1);
/**
 * /tests/Functional/Api/Controller/DefaultControllerTest.php
 */

namespace App\Tests\Functional\Api\Controller;

use App\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class DefaultControllerTest
 *
 * @package App\Tests\Functional\Api\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/';

    /**
     * @throws Throwable
     *
     * @testdox Test that default route returns success response
     */
    public function testThatDefaultRouteReturns200(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', $this->baseUrl);

        /** @var Response $response */
        $response = $client->getResponse();
        static::assertSame(200, $response->getStatusCode(), "Response:\n" . $response);
    }
}
