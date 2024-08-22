<?php

declare(strict_types=1);

namespace App\General\Domain\Service\Interfaces;

use Throwable;

/**
 * @package App\General
 */
interface MailerServiceInterface
{
    /**
     * Send mail to recipients
     *
     * @throws Throwable
     */
    public function sendMail(string $title, string $from, string $to, string $body): void;
}
