<?php

declare(strict_types=1);

namespace App\General\Application\Decorator;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Closure;
use Doctrine\DBAL\Schema\Index;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;

use function array_filter;
use function is_object;
use function str_contains;
use function str_starts_with;

/**
 * @package App\General
 */
class StopwatchDecorator
{
    public function __construct(
        private readonly AccessInterceptorValueHolderFactory $factory,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    public function decorate(object $service): object
    {
        $class = new ReflectionClass($service);
        $className = $class->getName();

        // Do not process core or extensions or already wrapped services
        if (
            $class->getFileName() === false
            || $class->isFinal()
            || str_starts_with($class->getName(), 'ProxyManagerGeneratedProxy')
            || str_contains($class->getName(), 'RequestStack')
            || str_contains($class->getName(), 'Mock_')
            || str_starts_with($class->getName(), Index::class)
        ) {
            return $service;
        }

        [$prefixInterceptors, $suffixInterceptors] = $this->getPrefixAndSuffixInterceptors($class, $className);

        try {
            $output = $this->factory->createProxy($service, $prefixInterceptors, $suffixInterceptors);
        } catch (Throwable) {
            $output = $service;
        }

        return $output;
    }

    /**
     * @return array{0: array<string, Closure>, 1: array<string, Closure>}
     */
    private function getPrefixAndSuffixInterceptors(ReflectionClass $class, string $className): array
    {
        $prefixInterceptors = [];
        $suffixInterceptors = [];

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, static fn ($method): bool => !$method->isStatic() && !$method->isFinal());

        foreach ($methods as $method) {
            $methodName = $method->getName();
            $eventName = "{$class->getShortName()}->{$methodName}";
            $prefixInterceptors[$methodName] = function () use ($eventName, $className): void {
                $this->stopwatch->start($eventName, $className);
            };
            $suffixInterceptors[$methodName] = function (
                mixed $vp,
                mixed $vi,
                mixed $vm,
                mixed $params,
                mixed &$returnValue
            ) use ($eventName): void {
                $this->stopwatch->stop($eventName);
                /**
                 * Decorate returned values as well
                 *
                 * Note that this might cause some weird errors on some edge
                 * cases - we should fix those when those happens...
                 */
                if (
                    is_object($returnValue)
                    && !$returnValue instanceof EntityInterface
                    && !$returnValue instanceof RestDtoInterface
                ) {
                    $returnValue = $this->decorate($returnValue);
                }
            };
        }

        return [$prefixInterceptors, $suffixInterceptors];
    }
}
