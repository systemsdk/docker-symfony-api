<?php

declare(strict_types=1);

namespace App\General\Application\Resource;

use App\General\Application\Collection\Traits\CollectionTrait;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use CallbackFilterIterator;
use Closure;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use IteratorIterator;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Throwable;

use function sprintf;

/**
 * @package App\General
 *
 * @method IteratorAggregate<int, RestResourceInterface|RestSmallResourceInterface> getAll()
 */
class ResourceCollection implements Countable
{
    use CollectionTrait;

    /**
     * @param IteratorAggregate<int, RestResourceInterface|RestSmallResourceInterface> $items
     */
    public function __construct(
        #[AutowireIterator('app.rest.resource')]
        private readonly IteratorAggregate $items,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Getter method for REST resource by entity class name.
     * One Entity can have one or more resources services. If entity relates to one resource service you can find
     * by classname. Otherwise you can pass additional param $interface (f.e. RestFindOneResourceInterface).
     */
    public function getEntityResource(
        string $className,
        ?string $interface = null
    ): RestResourceInterface|RestSmallResourceInterface {
        return $this->getFilteredItemByEntity($className, $interface) ?? throw new InvalidArgumentException(
            sprintf('Resource class does not exist for entity \'%s\'', $className),
        );
    }

    /**
     * Method to check if specified entity class REST resource exist or not in current collection.
     */
    public function hasEntityResource(?string $className = null, ?string $interface = null): bool
    {
        return $this->getFilteredItemByEntity($className ?? '', $interface) !== null;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function filter(string $className, ?string $interface = null): Closure
    {
        return static fn (
            RestResourceInterface|RestSmallResourceInterface $restResource
        ): bool => $restResource instanceof $className
            && ($interface === null || ($interface && $restResource instanceof $interface));
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getErrorMessage(string $className): string
    {
        return sprintf('Resource \'%s\' does not exist', $className);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $className, ?string $interface = null): RestResourceInterface|RestSmallResourceInterface
    {
        $result = null;

        if ($interface) {
            $result = $this->getFilteredItem($className, $interface);
        }

        if (!$interface || !$result) {
            $result = $this->getFilteredItem($className);
        }

        return $result ?? throw new InvalidArgumentException($this->getErrorMessage($className));
    }

    /**
     * {@inheritdoc}
     */
    public function has(?string $className = null, ?string $interface = null): bool
    {
        return $this->getFilteredItem($className ?? '', $interface) !== null;
    }

    private function getFilteredItem(string $className, ?string $interface = null): mixed
    {
        try {
            $iterator = $this->items->getIterator();
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());

            return null;
        }

        $filteredIterator = new CallbackFilterIterator(
            new IteratorIterator($iterator),
            $this->filter($className, $interface)
        );
        $filteredIterator->rewind();

        return $filteredIterator->current();
    }

    /**
     * Getter method to get filtered item by given entity class.
     */
    private function getFilteredItemByEntity(
        string $entityName,
        ?string $interface = null
    ): RestResourceInterface|RestSmallResourceInterface|null {
        try {
            $iterator = $this->items->getIterator();
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());

            return null;
        }

        $callback = static fn (
            RestResourceInterface|RestSmallResourceInterface $resource
        ): bool => $resource->getEntityName() === $entityName
            && (($interface && $resource instanceof $interface) || $resource instanceof RestResourceInterface);

        $filteredIterator = new CallbackFilterIterator(new IteratorIterator($iterator), $callback);
        $filteredIterator->rewind();

        return $filteredIterator->current();
    }
}
