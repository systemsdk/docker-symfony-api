<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/AuthenticationSuccessSubscriber.php
 */

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
    private LoginLoggerService $loginLoggerService;
    private UserRepository $userRepository;

    /**
     * Constructor
     */
    public function __construct(LoginLoggerService $loginLoggerService, UserRepository $userRepository)
    {
        $this->loginLoggerService = $loginLoggerService;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            //AuthenticationSuccessEvent::class
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
