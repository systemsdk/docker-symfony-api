<?php

declare(strict_types=1);

namespace App\User\Domain\Repository\Interfaces;

use App\User\Domain\Entity\User as Entity;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @package App\User
 */
interface UserRepositoryInterface
{
    /**
     * Method to check if specified username is available or not.
     *
     * @throws NonUniqueResultException
     */
    public function isUsernameAvailable(string $username, ?string $id = null): bool;

    /**
     * Method to check if specified email is available or not.
     *
     * @param string $email Email to check
     * @param string|null $id User id to ignore
     *
     * @throws NonUniqueResultException
     */
    public function isEmailAvailable(string $email, ?string $id = null): bool;

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not found.
     *
     * Method is override for performance reasons see link below.
     *
     * @see http://symfony2-document.readthedocs.org/en/latest/cookbook/security/entity_provider.html
     *      #managing-roles-in-the-database
     *
     * @param string $username The username
     * @param bool $uuid Is username parameter UUID or not
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $username, bool $uuid): ?Entity;
}
