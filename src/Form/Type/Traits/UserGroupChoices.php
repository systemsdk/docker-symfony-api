<?php
declare(strict_types = 1);
/**
 * /src/Form/Type/Traits/UserGroupChoices.php
 */

namespace App\Form\Type\Traits;

use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use Throwable;

/**
 * Trait UserGroupChoices
 *
 * @package App\Form\Type\Traits
 */
trait UserGroupChoices
{
    protected UserGroupResource $userGroupResource;

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
         *
         * @param UserGroup $userGroup
         */
        $iterator = static function (UserGroup $userGroup) use (&$choices): void {
            $name = $userGroup->getName() . ' [' . $userGroup->getRole()->getId() . ']';
            $choices[$name] = $userGroup->getId();
        };
        $userGroups = $this->userGroupResource->find();
        array_map($iterator, $userGroups);

        return $choices;
    }
}
