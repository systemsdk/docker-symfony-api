<?php

declare(strict_types=1);

namespace App\Tool\Transport\MessageHandler;

use App\Tool\Domain\Message\ExternalMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * TODO: This is external message handler example, you can delete it.
 *
 * @package App\Tool
 */
#[AsMessageHandler]
class ExternalHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ExternalMessage $message): void
    {
        $this->handleMessage($message);
    }

    /**
     * @throws Throwable
     */
    private function handleMessage(ExternalMessage $message): void
    {
        // some actions here
        $this->logger->info('Test external message processed');
    }
}
