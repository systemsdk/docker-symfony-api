<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/LockedUserSubscriber.php
 */

namespace App\EventSubscriber;

use App\Entity\LogLoginFailure;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Resource\LogLoginFailureResource;
use App\Security\SecurityUser;
use Doctrine\ORM\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Throwable;

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
     */
    public function __construct(UserRepository $userRepository, LogLoginFailureResource $logLoginFailureResource)
    {
        $this->userRepository = $userRepository;
        $this->logLoginFailureResource = $logLoginFailureResource;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string|array<int, string|int>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            //AuthenticationSuccessEvent::class
            Events::AUTHENTICATION_SUCCESS => [
                'onAuthenticationSuccess',
                128,
            ],
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    /**
     * @throws Throwable
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $this->getUser($event->getUser());

        if ($user === null) {
            throw new UnsupportedUserException('Unsupported user.');
        }

        if ($_ENV['LOCK_USER_ON_LOGIN_FAILURE_ATTEMPTS']
            && count($user->getLogsLoginFailure()) > $_ENV['LOCK_USER_ON_LOGIN_FAILURE_ATTEMPTS']) {
            throw new LockedException('Locked account.');
        }

        $this->logLoginFailureResource->reset($user);
    }

    /**
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
