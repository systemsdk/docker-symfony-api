<?php

declare(strict_types=1);

namespace App\User\Application\Security\Voter;

use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class IsUserHimselfVoter
 *
 * @package App\User
 */
class IsUserHimselfVoter extends Voter
{
    private const ATTRIBUTE = 'IS_USER_HIMSELF';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ATTRIBUTE && $subject instanceof User;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof SecurityUser && $user->getUuid() === $subject->getId();
    }
}
