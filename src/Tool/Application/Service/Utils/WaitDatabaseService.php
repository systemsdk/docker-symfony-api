<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils;

use App\Tool\Application\Service\Utils\Interfaces\WaitDatabaseServiceInterface;
use App\Tool\Domain\Service\Utils\Interfaces\CheckDatabaseConnectionServiceInterface;

/**
 * @package App\Tool
 */
class WaitDatabaseService implements WaitDatabaseServiceInterface
{
    public function __construct(
        private readonly CheckDatabaseConnectionServiceInterface $checkDatabaseConnectionService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function checkConnection(): bool
    {
        return $this->checkDatabaseConnectionService->checkConnection();
    }
}
