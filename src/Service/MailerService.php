<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Throwable;

/**
 * Class MailerService
 *
 * @package App\Service
 */
class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    /**
     * Send mail to recipients
     *
     * @throws Throwable
     */
    public function sendMail(string $title, string $from, string $to, string $body): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($title)
            ->html($body);

        $this->mailer->send($email);
    }
}
