<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Service;

use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\General\Infrastructure\Message\TestMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @package App\General
 */
class MessageService implements MessageServiceInterface
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    /**
     * TODO: This is example for creating test message, you can delete it.
     */
    public function sendTestMessage(string $someId): self
    {
        $this->bus->dispatch(new Envelope(new TestMessage($someId)));

        return $this;
    }
}
