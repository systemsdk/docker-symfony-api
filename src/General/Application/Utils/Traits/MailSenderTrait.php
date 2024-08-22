<?php

declare(strict_types=1);

namespace App\General\Application\Utils\Traits;

use App\General\Domain\Service\Interfaces\MailerServiceInterface;
use Throwable;
use Twig\Environment as Twig;

/**
 * @package App\General
 */
trait MailSenderTrait
{
    private MailerServiceInterface $mailerService;
    private string $appSenderEmail;
    private string $appErrorReceiverEmail;
    private bool $appEmailNotificationAboutError;
    private Twig $twig;

    public function setMailerService(
        MailerServiceInterface $mailerService,
        string $appSenderEmail,
        string $appErrorReceiverEmail,
        int $appEmailNotificationAboutError
    ): void {
        $this->mailerService = $mailerService;
        $this->appSenderEmail = $appSenderEmail;
        $this->appErrorReceiverEmail = $appErrorReceiverEmail;
        $this->appEmailNotificationAboutError = (bool)$appEmailNotificationAboutError;
    }

    public function setTwig(Twig $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @throws Throwable
     */
    public function sendErrorToMail(Throwable $exception): void
    {
        if ($this->appEmailNotificationAboutError) {
            $body = $this->twig->render('Emails/error.html.twig', [
                'errorMessage' => $exception->getMessage(),
            ]);
            $this->mailerService->sendMail(
                'An error has occurred.',
                $this->appSenderEmail,
                $this->appErrorReceiverEmail,
                $body
            );
        }
    }
}
