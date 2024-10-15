<?php

declare(strict_types=1);

namespace App\Tool\Transport\MessageHandler;

use App\Tool\Domain\Message\TestMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * If you need handling multiple - follow https://symfony.com/doc/current/messenger.html#handling-multiple-messages
 * TODO: This is handler example, you can delete it.
 *
 * @package App\Tool
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
