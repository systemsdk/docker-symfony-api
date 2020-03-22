<?php
declare(strict_types = 1);
/**
 * /src/EventListener/UserEntityEventListener.php
 */

namespace App\EventListener;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\User;
use LengthException;
use App\Security\SecurityUser;

/**
 * Class UserEntityEventListener
 *
 * @package App\EventSubscriber
 */
class UserEntityEventListener
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * Constructor
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * Doctrine lifecycle event for 'prePersist' event.
     *
     * @param LifecycleEventArgs $event
     *
     * @throws LengthException
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->process($event);
    }

    /**
     * Doctrine lifecycle event for 'preUpdate' event.
     *
     * @param LifecycleEventArgs $event
     *
     * @throws LengthException
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->process($event);
    }

    /**
     * @param LifecycleEventArgs $event
     *
     * @throws LengthException
     */
    private function process(LifecycleEventArgs $event): void
    {
        // Get user entity object
        $user = $event->getObject();

        // Valid user so lets change password
        if ($user instanceof User) {
            $this->changePassword($user);
        }
    }

    /**
     * Method to change user password whenever it's needed.
     *
     * @param User $user
     *
     * @throws LengthException
     */
    private function changePassword(User $user): void
    {
        // Get plain password from user entity
        $plainPassword = $user->getPlainPassword();

        // Yeah, we have new plain password set, so we need to encode it
        if ($plainPassword !== '') {
            if (strlen($plainPassword) < User::PASSWORD_MIN_LENGTH) {
                throw new LengthException(
                    'Too short password, should be at least ' . User::PASSWORD_MIN_LENGTH . ' symbols'
                );
            }

            // Password hash callback
            $callback = fn (string $plainPassword): string => $this->userPasswordEncoder
                ->encodePassword(new SecurityUser($user), $plainPassword);
            // Set new password and encode it with user encoder
            $user->setPassword($callback, $plainPassword);
            // And clean up plain password from entity
            $user->eraseCredentials();
        }
    }
}
