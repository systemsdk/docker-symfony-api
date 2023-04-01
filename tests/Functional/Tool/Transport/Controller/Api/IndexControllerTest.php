<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tool\Transport\Controller\Api;

use App\General\Transport\Utils\Tests\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class IndexControllerTest
 *
 * @package App\Tests
 */
class IndexControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that default route returns success response.')]
    public function testThatDefaultRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
    }
}
