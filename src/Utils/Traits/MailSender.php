<?php
declare(strict_types=1);

namespace App\Utils\Traits;

use App\Service\MailerService;
use Throwable;
use Psr\Cache\InvalidArgumentException;
use Twig\Environment as Twig;

trait MailSender
{
    private MailerService $mailerService;
    private Twig $twig;

    /**
     * @required
     *
     * @param MailerService $mailerService
     */
    public function setMailerService(MailerService $mailerService): void
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @required
     *
     * @param Twig $twig
     */
    public function setTwig(Twig $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @param Throwable|InvalidArgumentException $exception
     *
     * @throws Throwable
     */
    public function sendErrorToMail($exception): void
    {
        if ((bool)$_ENV['APP_EMAIL_NOTIFICATION_ABOUT_ERROR']) {
            $body = $this->twig->render('Emails/error.html.twig', ['errorMessage' => $exception->getMessage()]);
            $this->mailerService->sendMail(
                'An error has occurred.',
                $_ENV['APP_SENDER_EMAIL'],
                $_ENV['APP_ERROR_RECEIVER_EMAIL'],
                $body
            );
        }
    }
}
