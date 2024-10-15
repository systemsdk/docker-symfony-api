<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Service;

use App\General\Domain\Message\Interfaces\MessageHighInterface;
use App\General\Domain\Message\Interfaces\MessageLowInterface;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
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
     * {@inheritdoc}
     *
     * @throws ExceptionInterface
     */
    public function sendMessage(MessageHighInterface|MessageLowInterface $message): self
    {
        $this->bus->dispatch(new Envelope($message));

        return $this;
    }
}
