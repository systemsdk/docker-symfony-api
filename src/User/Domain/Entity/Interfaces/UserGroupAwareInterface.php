<?php

declare(strict_types=1);

namespace App\User\Domain\Entity\Interfaces;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Domain\Entity\UserGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package App\User
 */
interface UserGroupAwareInterface extends EntityInterface
{
    /**
     * @return Collection<int, UserGroup>|ArrayCollection<int, UserGroup>
     */
    public function getUserGroups(): Collection | ArrayCollection;

    /**
     * Method to attach new userGroup to current user OR api key.
     */
    public function addUserGroup(UserGroup $userGroup): mixed;

    /**
     * Method to remove specified userGroup from current user OR api key.
     */
    public function removeUserGroup(UserGroup $userGroup): mixed;

    /**
     * Method to remove all many-to-many userGroup relations from current user OR api key.
     */
    public function clearUserGroups(): mixed;
}
