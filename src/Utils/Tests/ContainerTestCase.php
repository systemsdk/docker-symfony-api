<?php
declare(strict_types = 1);
/**
 * /src/Utils/Tests/ContainerTestCase.php
 */

namespace App\Utils\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContainerTestCase
 *
 * @package App\Utils\Tests;
 */
abstract class ContainerTestCase extends KernelTestCase
{
    /**
     * @var ContainerInterface|null
     */
    private $testContainer;

    /**
     * Getter method for container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if (!($this->testContainer instanceof ContainerInterface)) {
            self::bootKernel();
            $this->testContainer = self::$container;
        }

        return $this->testContainer;
    }
}
