<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

/**
 * @package App\Tests\Unit
 */
class ExampleTest extends KernelTestCase
{
    /**
     * A basic test example.
     *
     * @throws Throwable
     */
    public function testBasicTest(): void
    {
        self::assertEquals(true, true);
    }
}
