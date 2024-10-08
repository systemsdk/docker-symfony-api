<?php

declare(strict_types=1);

namespace App\User\Transport\Command\User;

use App\General\Transport\Command\Traits\ExecuteMultipleCommandTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;

/**
 * @package App\User
 */
#[AsCommand(
    name: 'user:management',
    description: 'Console command to manage users and user groups',
)]
class ManagementCommand extends Command
{
    use ExecuteMultipleCommandTrait;

    /**
     * @throws LogicException
     */
    public function __construct()
    {
        parent::__construct();

        $this->setChoices([
            ListUsersCommand::NAME => 'List users',
            ListUserGroupsCommand::NAME => 'List user groups',
            CreateUserCommand::NAME => 'Create user',
            CreateUserGroupCommand::NAME => 'Create user group',
            EditUserCommand::NAME => 'Edit user',
            EditUserGroupCommand::NAME => 'Edit user group',
            RemoveUserCommand::NAME => 'Remove user',
            RemoveUserGroupCommand::NAME => 'Remove user group',
            '0' => 'Exit',
        ]);
    }
}
