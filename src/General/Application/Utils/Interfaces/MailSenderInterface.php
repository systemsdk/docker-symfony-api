<?php

declare(strict_types=1);

namespace App\General\Application\Utils\Interfaces;

use App\General\Domain\Service\Interfaces\MailerServiceInterface;
use Throwable;
use Twig\Environment as Twig;

/**
 * @package App\General
 */
interface MailSenderInterface
{
    public function setMailerService(
        MailerServiceInterface $mailerService,
        string $appSenderEmail,
        string $appErrorReceiverEmail,
        int $appEmailNotificationAboutError
    ): void;

    public function setTwig(Twig $twig): void;

    public function sendErrorToMail(Throwable $exception): void;
}
