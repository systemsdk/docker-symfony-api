<?php
declare(strict_types = 1);
/**
 * /src/Command/User/ManagementCommand.php
 */

namespace App\Command\User;

use App\Command\Traits\ExecuteMultipleCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Class ManagementCommand
 *
 * @package App\Command\User
 */
class ManagementCommand extends Command
{
    // Traits
    use ExecuteMultipleCommand;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct()
    {
        parent::__construct('user:management');
        $this->setDescription('Console command to manage users and user groups');
        /** @psalm-suppress InvalidScalarArgument */
        $this->setChoices([
            'user:list' => 'List users',
            'user:list-groups' => 'List user groups',
            'user:create' => 'Create user',
            'user:create-group' => 'Create user group',
            'user:edit' => 'Edit user',
            'user:edit-group' => 'Edit user group',
            'user:remove' => 'Remove user',
            'user:remove-group' => 'Remove user group',
            false => 'Exit',
        ]);
    }
}
