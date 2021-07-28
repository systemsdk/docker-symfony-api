<?php

declare(strict_types=1);

namespace App\Form\Type\Traits;

use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use Throwable;

use function array_map;

/**
 * Trait UserGroupChoices
 *
 * @package App\Form\Type\Traits
 *
 * @property UserGroupResource $userGroupResource
 */
trait UserGroupChoices
{
    /**
     * Method to create choices array for user groups.
     *
     * @throws Throwable
     *
     * @return array<string, string>
     */
    protected function getUserGroupChoices(): array
    {
        // Initialize output
        $choices = [];
        /**
         * Lambda function to iterate all user groups and to create necessary choices array.
         */
        $iterator = static function (UserGroup $userGroup) use (&$choices): void {
            $name = $userGroup->getName() . ' [' . $userGroup->getRole()->getId() . ']';
            $choices[$name] = $userGroup->getId();
        };

        array_map($iterator, $this->userGroupResource->find());

        return $choices;
    }
}
