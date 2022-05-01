<?php

declare(strict_types=1);

namespace App\General\Transport\Rest;

use App\General\Application\Collection\Traits\CollectionTrait;
use App\General\Transport\Rest\Interfaces\ControllerInterface;
use Closure;
use Countable;
use IteratorAggregate;
use Psr\Log\LoggerInterface;

use function sprintf;

/**
 * Class ControllerCollection
 *
 * @package App\General
 *
 * @method ControllerInterface get(string $className)
 * @method IteratorAggregate<int, ControllerInterface> getAll()
 *
 * @template T<ControllerInterface>
 */
class ControllerCollection implements Countable
{
    use CollectionTrait;

    /**
     * Constructor
     *
     * @phpstan-param IteratorAggregate<int, ControllerInterface> $items
     */
    public function __construct(
        protected IteratorAggregate $items,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(string $className): string
    {
        return sprintf('REST controller \'%s\' does not exist', $className);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(string $className): Closure
    {
        return static fn (ControllerInterface $restController): bool => $restController instanceof $className;
    }
}
