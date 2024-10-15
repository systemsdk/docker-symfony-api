<?php

declare(strict_types=1);

namespace App\Tool\Domain\Message;

/**
 * TODO: This is external message example, you can delete it.
 *
 * @package App\Tool
 */
class ExternalMessage
{
    public function __construct(
        private readonly string $service,
        private readonly string $externalId,
    ) {
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }
}
