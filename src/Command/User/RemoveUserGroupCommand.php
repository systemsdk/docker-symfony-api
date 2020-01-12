<?php
declare(strict_types = 1);
/**
 * /src/Command/User/RemoveUserGroupCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\StyleSymfony;
use Symfony\Component\Console\Exception\LogicException;
use App\Resource\UserGroupResource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\UserGroup;
use Throwable;

/**
 * Class RemoveUserGroupCommand
 *
 * @package App\Command\User
 */
class RemoveUserGroupCommand extends Command
{
    // Traits
    use StyleSymfony;

    private UserGroupResource $userGroupResource;
    private UserHelper $userHelper;


    /**
     * Constructor
     *
     * @param UserGroupResource $userGroupResource
     * @param UserHelper        $userHelper
     *
     * @throws LogicException
     */
    public function __construct(UserGroupResource $userGroupResource, UserHelper $userHelper)
    {
        parent::__construct('user:remove-group');

        $this->userGroupResource = $userGroupResource;
        $this->userHelper = $userHelper;

        $this->setDescription('Console command to remove existing user group');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);

        $userGroup = $this->userHelper->getUserGroup($io, 'Which user group you want to remove?');
        $message = null;

        if ($userGroup instanceof UserGroup) {
            // Delete user group
            $this->userGroupResource->delete($userGroup->getId());
            $message = 'User group removed - have a nice day';
        }

        if ($input->isInteractive()) {
            $message ??= 'Nothing changed - have a nice day';
            $io->success($message);
        }

        return 0;
    }
}
