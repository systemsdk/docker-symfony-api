<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Service;

use App\General\Domain\Service\Interfaces\MailerServiceInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @package App\General
 */
class MailerService implements MailerServiceInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * {@inheritdoc}
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
