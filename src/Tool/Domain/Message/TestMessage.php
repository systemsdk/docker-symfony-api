<?php

declare(strict_types=1);

namespace App\Tool\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

/**
 * TODO: This is message example, you can delete it.
 *
 * @package App\Tool
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
