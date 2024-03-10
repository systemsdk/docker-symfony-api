<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\General\Domain\Utils\JSON;
use ArrayObject;
use JsonException;
use Stringable;

/**
 * @package App\Tests
 */
class StringableArrayObject extends ArrayObject implements Stringable
{
    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        $iterator = static fn (mixed $input): mixed => $input instanceof Stringable ? (string)$input : $input;

        return JSON::encode(array_map($iterator, $this->getArrayCopy()));
    }
}
