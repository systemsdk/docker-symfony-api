<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class DocumentationTest extends WebTestCase
{
    /**
     * @throws Throwable
     */
    #[TestDox('Test that documentation (/api/doc) is working.')]
    public function testThatDocumentationUiWorks(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/api/doc');

        static::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that documentation json (/api/doc.json) is working.')]
    public function testThatDocumentationJsonWorks(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/api/doc.json');

        static::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
