<?php
declare(strict_types = 1);
/**
 * /src/Collection/Traits/Collection.php
 */

namespace App\Collection\Traits;

use Psr\Log\LoggerInterface;
use CallbackFilterIterator;
use Closure;
use InvalidArgumentException;
use IteratorAggregate;
use IteratorIterator;
use Throwable;

/**
 * Trait Collection
 *
 * @package App\Collection\Traits
 */
trait Collection
{
    private IteratorAggregate $items;
    private LoggerInterface $logger;

    /**
     * @param string $className
     *
     * @return Closure
     */
    abstract public function filter(string $className): Closure;

    /**
     * @param string $className
     *
     * @throws InvalidArgumentException
     */
    abstract public function error(string $className): void;

    /**
     * Getter method for RestResource class.
     *
     * @param string $className
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function get(string $className)
    {
        $current = $this->getFilteredItem($className);

        if ($current === null) {
            $this->error($className);
        }

        return $current;
    }

    /**
     * @return IteratorAggregate
     */
    public function getAll(): IteratorAggregate
    {
        return $this->items;
    }

    /**
     * Method to check if specified resource exists or not in this Collection.
     *
     * @param string|null $className
     *
     * @return bool
     */
    public function has(?string $className = null): bool
    {
        return $className === null ? false : $this->getFilteredItem($className) !== null;
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->items);
    }

    /**
     * @param string $className
     *
     * @return mixed|null
     */
    private function getFilteredItem(string $className)
    {
        try {
            $iterator = $this->items->getIterator();
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());

            return null;
        }

        $filteredIterator = new CallbackFilterIterator(new IteratorIterator($iterator), $this->filter($className));
        $filteredIterator->rewind();

        return $filteredIterator->current();
    }
}
