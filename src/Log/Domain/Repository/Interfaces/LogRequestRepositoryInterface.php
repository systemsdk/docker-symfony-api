<?php

declare(strict_types=1);

namespace App\Log\Domain\Repository\Interfaces;

use Exception;

/**
 * @package App\Log
 */
interface LogRequestRepositoryInterface
{
    /**
     * Helper method to clean history data from log_request table.
     *
     * @throws Exception
     */
    public function cleanHistory(): int;
}
