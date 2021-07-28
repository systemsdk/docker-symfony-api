<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\DBAL\Types\EnumLogLoginType;
use App\Repository\UserRepository;
use App\Service\LoginLoggerService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

/**
 * Class AuthenticationSuccessSubscriber
 *
 * @package App\EventSubscriber
 */
class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoginLoggerService $loginLoggerService,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
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
            ->setUser($this->userRepository->loadUserByUsername($event->getUser()->getUsername(), true))
            ->process(EnumLogLoginType::TYPE_SUCCESS);
    }
}
