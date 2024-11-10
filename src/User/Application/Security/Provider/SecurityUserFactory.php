<?php

declare(strict_types=1);

namespace App\User\Application\Security\Provider;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Override;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

use function sprintf;

/**
 * @package App\User
 *
 * @template-implements UserProviderInterface<SecurityUser>
 */
class SecurityUserFactory implements UserProviderInterface
{
    /**
     * @param \App\User\Infrastructure\Repository\UserRepository $userRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     */
    #[Override]
    public function loadUserByIdentifier(string $identifier): SecurityUser
    {
        $user = $this->userRepository->loadUserByIdentifier(
            $identifier,
            (bool)preg_match('#' . Requirement::UUID_V1 . '#', $identifier)
        );

        if (!($user instanceof User)) {
            throw new UserNotFoundException(sprintf('User not found for UUID: "%s".', $identifier));
        }

        return new SecurityUser($user, $this->rolesService->getInheritedRoles($user->getRoles()));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    #[Override]
    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!($user instanceof SecurityUser)) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        $userEntity = $this->userRepository->find($user->getUserIdentifier());

        if (!($userEntity instanceof User)) {
            throw new UserNotFoundException(sprintf('User not found for UUID: "%s".', $user->getUserIdentifier()));
        }

        return new SecurityUser($userEntity, $this->rolesService->getInheritedRoles($userEntity->getRoles()));
    }
}
