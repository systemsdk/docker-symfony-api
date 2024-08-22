<?php

declare(strict_types=1);

namespace App\User\Transport\Form\Type\Traits;

use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use Throwable;

use function array_map;

/**
 * @package App\User
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
