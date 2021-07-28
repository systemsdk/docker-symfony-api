<?php

declare(strict_types=1);

namespace App\Utils\Traits;

use App\Service\MailerService;
use Psr\Cache\InvalidArgumentException;
use Throwable;
use Twig\Environment as Twig;

/**
 * Trait MailSenderTrait
 *
 * @package App\Utils\Traits
 */
trait MailSenderTrait
{
    private MailerService $mailerService;
    private string $appSenderEmail;
    private string $appErrorReceiverEmail;
    private bool $appEmailNotificationAboutError;
    private Twig $twig;

    /**
     * @required
     */
    public function setMailerService(
        MailerService $mailerService,
        string $appSenderEmail,
        string $appErrorReceiverEmail,
        int $appEmailNotificationAboutError
    ): void {
        $this->mailerService = $mailerService;
        $this->appSenderEmail = $appSenderEmail;
        $this->appErrorReceiverEmail = $appErrorReceiverEmail;
        $this->appEmailNotificationAboutError = (bool)$appEmailNotificationAboutError;
    }

    /**
     * @required
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
        if ($this->appEmailNotificationAboutError) {
            $body = $this->twig->render('Emails/error.html.twig', ['errorMessage' => $exception->getMessage()]);
            $this->mailerService->sendMail(
                'An error has occurred.',
                $this->appSenderEmail,
                $this->appErrorReceiverEmail,
                $body
            );
        }
    }
}
