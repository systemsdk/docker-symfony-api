<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool
 */
interface WaitDatabaseServiceInterface
{
    /**
     * Check if database connection is possible. Throwing an exception if connection is not possible.
     *
     * @throws Exception
     */
    public function checkConnection(): bool;
}
