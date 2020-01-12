<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExampleTest extends WebTestCase
{
    /**
     * A basic test example.
     *
     * @throws Throwable
     */
    public function testBasicTest(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/command-scheduler/list');

        /** @var Response $response */
        $response = $client->getResponse();

        static::assertInstanceOf(Response::class, $response);
        // check for 401 due to allow only for user with admin role
        static::assertSame(401, $response->getStatusCode());

        unset($response, $client);
    }
}
