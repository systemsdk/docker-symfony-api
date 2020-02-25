<?php
declare(strict_types = 1);
/**
 * /src/Command/User/EditUserGroupCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\StyleSymfony;
use App\Resource\UserGroupResource;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\UserGroup as UserGroupEntity;
use App\DTO\UserGroup\UserGroupPatch as UserGroupDto;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use App\Form\Type\Console\UserGroupType;
use Throwable;

/**
 * Class EditUserGroupCommand
 *
 * @package App\Command\User
 */
class EditUserGroupCommand extends Command
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
        parent::__construct('user:edit-group');

        $this->userGroupResource = $userGroupResource;
        $this->userHelper = $userHelper;

        $this->setDescription('Command to edit existing user group');
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
        $userGroup = $this->userHelper->getUserGroup($io, 'Which user group you want to edit?');
        $message = null;

        if ($userGroup instanceof UserGroupEntity) {
            $message = $this->updateUserGroup($input, $output, $userGroup);
        }

        if ($input->isInteractive()) {
            $message ??= 'Nothing changed - have a nice day';
            $io->success($message);
        }

        return 0;
    }

    /**
     * Method to update specified user group entity via specified form.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param UserGroupEntity $userGroup
     *
     * @throws Throwable
     *
     * @return string
     */
    protected function updateUserGroup(
        InputInterface $input,
        OutputInterface $output,
        UserGroupEntity $userGroup
    ): string {
        // Load entity to DTO
        $dtoLoaded = new UserGroupDto();
        $dtoLoaded->load($userGroup);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserGroupDto $dtoEdit */
        $dtoEdit = $helper->interactUsingForm(UserGroupType::class, $input, $output, ['data' => $dtoLoaded]);
        // Patch user group
        $this->userGroupResource->patch($userGroup->getId(), $dtoEdit);

        return 'User group updated - have a nice day';
    }
}
