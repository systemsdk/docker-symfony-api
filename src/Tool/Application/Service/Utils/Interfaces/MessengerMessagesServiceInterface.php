<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool
 */
interface MessengerMessagesServiceInterface
{
    /**
     * @throws Exception
     */
    public function cleanUp(): int;
}
