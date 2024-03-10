<?php

declare(strict_types=1);

namespace App\User\Application\Security\Voter;

use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @package App\User
 *
 * @template TAttribute of string
 * @template TSubject of mixed
 *
 * @extends Voter<TAttribute, TSubject>
 */
class IsUserHimselfVoter extends Voter
{
    private const string ATTRIBUTE = 'IS_USER_HIMSELF';

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

        return $user instanceof SecurityUser && $subject instanceof User && $user->getUuid() === $subject->getId();
    }
}
