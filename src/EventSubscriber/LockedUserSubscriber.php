<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/LockedUserSubscriber.php
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Repository\UserRepository;
use App\Resource\LogLoginFailureResource;
use App\Entity\LogLoginFailure;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Throwable;
use Doctrine\ORM\ORMException;
use App\Security\SecurityUser;

/**
 * Class LockedUserSubscriber
 *
 * @package App\EventSubscriber
 */
class LockedUserSubscriber implements EventSubscriberInterface
{
    private UserRepository $userRepository;
    private LogLoginFailureResource $logLoginFailureResource;

    /**
     * Constructor
     *
     * @param UserRepository          $userRepository
     * @param LogLoginFailureResource $logLoginFailureResource
     */
    public function __construct(UserRepository $userRepository, LogLoginFailureResource $logLoginFailureResource)
    {
        $this->userRepository = $userRepository;
        $this->logLoginFailureResource = $logLoginFailureResource;
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
     * @return array<string, string|array<int, string|int>> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => [ //AuthenticationSuccessEvent::class
                'onAuthenticationSuccess',
                128,
            ],
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    /**
     * @param AuthenticationSuccessEvent $event
     *
     * @throws Throwable
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $this->getUser($event->getUser());

        if ($user === null) {
            throw new UnsupportedUserException('Unsupported user.');
        }

        if (count($user->getLogsLoginFailure()) > 10) {
            throw new LockedException('Locked account.');
        }

        $this->logLoginFailureResource->reset($user);
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @throws Throwable
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $token = $event->getException()->getToken();

        if ($token !== null) {
            $user = $this->getUser($token->getUser());

            if ($user !== null) {
                $this->logLoginFailureResource->save(new LogLoginFailure($user), true);
            }

            $token->setAuthenticated(false);
        }
    }

    /**
     * @param string|object $user
     *
     * @throws ORMException
     *
     * @return User|null
     */
    private function getUser($user): ?User
    {
        $output = null;

        if (is_string($user)) {
            $output = $this->userRepository->loadUserByUsername($user, false);
        } elseif ($user instanceof SecurityUser) {
            $output = $this->userRepository->loadUserByUsername($user->getUsername(), true);
        }

        return $output;
    }
}
