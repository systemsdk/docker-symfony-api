<?php

declare(strict_types=1);

namespace App\General\Infrastructure\MessageHandler;

use App\General\Infrastructure\Message\TestMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * If you need handling multiple - follow https://symfony.com/doc/current/messenger.html#handling-multiple-messages
 * TODO: This is handler example, you can delete it.
 *
 * @package App\General
 */
#[AsMessageHandler]
class TestHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(TestMessage $message): void
    {
        $this->handleMessage($message);
    }

    /**
     * @throws Throwable
     */
    private function handleMessage(TestMessage $message): void
    {
        // some actions here
        $this->logger->info('Test message processed');
    }
}
