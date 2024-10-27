<?php

declare(strict_types=1);

namespace App\Log\Application\Service\Utils;

use App\Log\Application\Service\Utils\Interfaces\CleanupLogServiceInterface;
use App\Log\Domain\Repository\Interfaces\LogLoginRepositoryInterface;
use App\Log\Domain\Repository\Interfaces\LogRequestRepositoryInterface;

/**
 * @package App\Log
 */
class CleanupLogService implements CleanupLogServiceInterface
{
    public function __construct(
        private readonly LogLoginRepositoryInterface $logLoginRepository,
        private readonly LogRequestRepositoryInterface $logRequestRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup(): bool
    {
        $this->logLoginRepository->cleanHistory();
        $this->logRequestRepository->cleanHistory();

        return true;
    }
}
