<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/AuthenticationSuccessSubscriber.php
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\LoginLoggerService;
use App\Repository\UserRepository;
use App\Doctrine\DBAL\Types\EnumLogLoginType;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
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
     *
     * @param LoginLoggerService $loginLoggerService
     * @param UserRepository     $userRepository
     */
    public function __construct(LoginLoggerService $loginLoggerService, UserRepository $userRepository)
    {
        $this->loginLoggerService = $loginLoggerService;
        $this->userRepository = $userRepository;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array<string, string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    /**
     * Method to log user successfully login to database.
     *
     * This method is called when 'lexik_jwt_authentication.on_authentication_success' event is broadcast.
     *
     * @param AuthenticationSuccessEvent $event
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
