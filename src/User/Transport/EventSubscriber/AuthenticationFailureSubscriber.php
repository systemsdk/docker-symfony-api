<?php

declare(strict_types=1);

namespace App\User\Transport\EventSubscriber;

use App\Log\Application\Service\Interfaces\LoginLoggerServiceInterface;
use App\Log\Domain\Enum\LogLogin;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

/**
 * @package App\User
 */
class AuthenticationFailureSubscriber implements EventSubscriberInterface
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
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * Method to log login failures to database.
     *
     * This method is called when following event is broadcast;
     *  - \Lexik\Bundle\JWTAuthenticationBundle\Events::AUTHENTICATION_FAILURE
     *
     * @throws Throwable
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $token = $event->getException()->getToken();
        $user = $token?->getUser();

        // Fetch user entity
        if ($token !== null && $user !== null) {
            $identifier = $user->getUserIdentifier();
            $this->loginLoggerService->setUser($this->userRepository->loadUserByIdentifier($identifier, false));
        }

        $this->loginLoggerService->process(LogLogin::FAILURE);
    }
}
