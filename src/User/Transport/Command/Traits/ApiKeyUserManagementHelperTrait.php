<?php

declare(strict_types=1);

namespace App\User\Transport\Command\Traits;

use App\General\Transport\Command\Traits\GetApplicationTrait;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\User
 */
trait ApiKeyUserManagementHelperTrait
{
    use GetApplicationTrait;

    abstract public function getRolesService(): RolesServiceInterface;

    /**
     * Method to create user groups via existing 'user:create-group' command.
     *
     * @throws Throwable
     */
    protected function createUserGroups(OutputInterface $output): void
    {
        $command = $this->getApplication()->find('user:create-group');

        // Iterate roles and create user group for each one
        foreach ($this->getRolesService()->getRoles() as $role) {
            $arguments = [
                'command' => 'user:create-group',
                '--name' => $this->getRolesService()->getRoleLabel($role),
                '--role' => $role,
                '-n' => true,
            ];

            $input = new ArrayInput($arguments);
            $input->setInteractive(false);

            $command->run($input, $output);
        }
    }
}
