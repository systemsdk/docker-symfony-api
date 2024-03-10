<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Message;

use App\General\Infrastructure\Message\Interfaces\MessageHighInterface;

/**
 * TODO: This is message example, you can delete it.
 *
 * @package App\General
 */
class TestMessage implements MessageHighInterface
{
    public function __construct(
        private readonly string $someId,
    ) {
    }

    public function getSomeId(): string
    {
        return $this->someId;
    }
}
