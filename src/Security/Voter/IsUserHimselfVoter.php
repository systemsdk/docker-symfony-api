<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class IsUserHimselfVoter
 *
 * @package App\Security
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
