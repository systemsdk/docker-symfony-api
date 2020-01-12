<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/AuthenticationFailureSubscriber.php
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\LoginLoggerService;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use App\Doctrine\DBAL\Types\EnumLogLoginType;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Doctrine\ORM\ORMException;
use Throwable;

/**
 * Class AuthenticationFailureSubscriber
 *
 * @package App\EventSubscriber
 */
class AuthenticationFailureSubscriber implements EventSubscriberInterface
{
    protected LoginLoggerService $loginLoggerService;
    protected UserRepository $userRepository;

    /**
     * Constructor
     *
     * @param LoginLoggerService    $loginLoggerService
     * @param UserRepository $userRepository
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
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * Method to log login failures to database.
     *
     * This method is called when '\Lexik\Bundle\JWTAuthenticationBundle\Events::AUTHENTICATION_FAILURE'
     * event is broadcast.
     *
     * @param AuthenticationFailureEvent $event
     *
     * @throws ORMException|Throwable
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $token = $event->getException()->getToken();

        // Fetch user entity
        if ($token !== null && is_string($token->getUser())) {
            /** @var string $username */
            $username = $token->getUser();
            $this->loginLoggerService->setUser($this->userRepository->loadUserByUsername($username));
        }

        $this->loginLoggerService->process(EnumLogLoginType::TYPE_FAILURE);
    }
}
