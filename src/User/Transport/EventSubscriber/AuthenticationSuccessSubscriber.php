<?php

declare(strict_types=1);

namespace App\User\Transport\EventSubscriber;

use App\Log\Application\Service\Interfaces\LoginLoggerServiceInterface;
use App\Log\Domain\Enum\LogLogin;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

/**
 * @package App\User
 */
class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    /**
     * @param \App\User\Infrastructure\Repository\UserRepository $userRepository
     */
    public function __construct(
        private readonly LoginLoggerServiceInterface $loginLoggerService,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    /**
     * Method to log user successfully login to database.
     *
     * This method is called when following event is broadcast
     *  - lexik_jwt_authentication.on_authentication_success
     *
     * @throws Throwable
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $this->loginLoggerService
            ->setUser($this->userRepository->loadUserByIdentifier($event->getUser()->getUserIdentifier(), true))
            ->process(LogLogin::SUCCESS);
    }
}
