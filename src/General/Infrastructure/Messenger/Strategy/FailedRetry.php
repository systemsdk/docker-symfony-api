<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Messenger\Strategy;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Throwable;

/**
 * @package App
 */
class FailedRetry implements RetryStrategyInterface
{
    public function __construct(
        private readonly bool $isRetryable,
        private readonly int $retryWaitingTime,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * In case false - messages from "failed" transport will not be sent for the second retry (messenger:failed:retry).
     */
    public function isRetryable(Envelope $message, ?Throwable $throwable = null): bool
    {
        return $this->isRetryable;
    }

    /**
     * {@inheritdoc}
     */
    public function getWaitingTime(Envelope $message, ?Throwable $throwable = null): int
    {
        return $this->retryWaitingTime;
    }
}
