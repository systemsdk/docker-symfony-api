<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\Tool\Application\Service\Interfaces\MessengerMessagesServiceInterface;
use App\Tool\Domain\Repository\Interfaces\MessengerMessagesRepositoryInterface;

/**
 * @package App\Tool
 */
class MessengerMessagesService implements MessengerMessagesServiceInterface
{
    public function __construct(
        private readonly MessengerMessagesRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUp(): int
    {
        return $this->repository->cleanUp();
    }
}