<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\General\Transport\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class DocumentationTest
 *
 * @package App\Tests
 */
class DocumentationTest extends WebTestCase
{
    /**
     * @testdox Test that documentation (/api/doc) is working.
     *
     * @throws Throwable
     */
    public function testThatDocumentationUiWorks(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/api/doc');

        static::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @testdox Test that documentation json (/api/doc.json) is working.
     *
     * @throws Throwable
     */
    public function testThatDocumentationJsonWorks(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/api/doc.json');

        static::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
