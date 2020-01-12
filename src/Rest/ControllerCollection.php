<?php
declare(strict_types = 1);
/**
 * /src/Rest/ControllerCollection.php
 */

namespace App\Rest;

use App\Rest\Interfaces\ControllerInterface;
use App\Collection\Traits\Collection;
use Closure;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Class ControllerCollection
 *
 * @package App\Rest
 *
 * @property IteratorAggregate|IteratorAggregate<int, ControllerInterface> $items
 *
 * @method ControllerInterface                         get(string $className)
 * @method IteratorAggregate<int, ControllerInterface> getAll(): IteratorAggregate
 */
class ControllerCollection implements Countable
{
    // Traits
    use Collection;

    /**
     * Constructor
     *
     * @param IteratorAggregate|IteratorAggregate<int, ControllerInterface> $controllers
     */
    public function __construct(IteratorAggregate $controllers)
    {
        $this->items = $controllers;
    }

    /**
     * @param string $className
     *
     * @throws InvalidArgumentException
     */
    public function error(string $className): void
    {
        $message = sprintf(
            'REST controller \'%s\' does not exists',
            $className
        );

        throw new InvalidArgumentException($message);
    }

    /**
     * @param string $className
     *
     * @return Closure
     */
    public function filter(string $className): Closure
    {
        return static function (ControllerInterface $restController) use ($className): bool {
            return $restController instanceof $className;
        };
    }
}
