<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\General\Transport\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class DefaultControllerTest
 *
 * @package App\Tests\Functional\Controller\Api
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

        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }
}
