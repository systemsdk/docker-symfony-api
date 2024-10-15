<?php

declare(strict_types=1);

namespace App\Tool\Domain\Repository\Interfaces;

use Exception;

/**
 * @package App\Tool
 */
interface MessengerMessagesRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function cleanUp(): int;
}
