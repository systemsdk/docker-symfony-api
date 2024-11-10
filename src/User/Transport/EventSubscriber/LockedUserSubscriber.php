<?php

declare(strict_types=1);

namespace App\User\Transport\EventSubscriber;

use App\Log\Application\Resource\LogLoginFailureResource;
use App\Log\Domain\Entity\LogLoginFailure;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Throwable;

use function assert;
use function count;
use function is_string;

/**
 * @package App\User
 */
class LockedUserSubscriber implements EventSubscriberInterface
{
    /**
     * @param \App\User\Infrastructure\Repository\UserRepository $userRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LogLoginFailureResource $logLoginFailureResource,
        private readonly RequestStack $requestStack,
        private readonly int $lockUserOnLoginFailureAttempts,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => [
                'onAuthenticationSuccess',
                128,
            ],
            Events::AUTHENTICATION_SUCCESS => [
                'onAuthenticationSuccess',
                128,
            ],
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * @throws Throwable
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $this->getUser($event->getUser()) ?? throw new UnsupportedUserException('Unsupported user.');

        if (
            $this->lockUserOnLoginFailureAttempts
            && count($user->getLogsLoginFailure()) > $this->lockUserOnLoginFailureAttempts
        ) {
            throw new LockedException('Locked account.');
        }

        $this->logLoginFailureResource->reset($user);
    }

    /**
     * @throws Throwable
     */
    public function onAuthenticationFailure(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        assert($request instanceof Request);
        $user = $this->getUser(
            (string)($request->query->get('username') ?? $request->request->get('username', ''))
        );

        if ($user !== null) {
            $this->logLoginFailureResource->save(new LogLoginFailure($user), true);
        }
    }

    /**
     * @throws Throwable
     */
    private function getUser(string | object $user): ?User
    {
        return match (true) {
            is_string($user) => $this->userRepository->loadUserByIdentifier($user, false),
            $user instanceof SecurityUser => $this->userRepository->loadUserByIdentifier(
                $user->getUserIdentifier(),
                true
            ),
            default => null,
        };
    }
}
