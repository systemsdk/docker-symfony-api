<?php

declare(strict_types=1);

namespace App\General\Domain\Service\Interfaces;

use App\General\Domain\Message\Interfaces\MessageHighInterface;
use App\General\Domain\Message\Interfaces\MessageLowInterface;
use Throwable;

/**
 * @package App\General
 */
interface MessageServiceInterface
{
    /**
     * @throws Throwable
     */
    public function sendMessage(MessageHighInterface|MessageLowInterface $message): self;
}
