<?php
declare(strict_types = 1);
/**
 * /src/Command/User/EditUserCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\StyleSymfony;
use Symfony\Component\Console\Exception\LogicException;
use App\Resource\UserResource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\User as UserEntity;
use App\DTO\User\UserPatch as UserDto;
use App\Form\Type\Console\UserType;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Throwable;

/**
 * Class EditUserCommand
 *
 * @package App\Command\User
 */
class EditUserCommand extends Command
{
    // Traits
    use StyleSymfony;

    private UserResource $userResource;
    private UserHelper $userHelper;

    /**
     * Constructor
     *
     * @param UserResource $userResource
     * @param UserHelper   $userHelper
     *
     * @throws LogicException
     */
    public function __construct(UserResource $userResource, UserHelper $userHelper)
    {
        parent::__construct('user:edit');

        $this->userResource = $userResource;
        $this->userHelper = $userHelper;

        $this->setDescription('Command to edit existing user');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        // Get user entity
        $user = $this->userHelper->getUser($io, 'Which user you want to edit?');
        $message = null;

        if ($user instanceof UserEntity) {
            $message = $this->updateUser($input, $output, $user);
        }

        if ($input->isInteractive()) {
            $message ??= 'Nothing changed - have a nice day';
            $io->success($message);
        }

        return 0;
    }

    /**
     * Method to update specified user entity via specified form.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param UserEntity $user
     *
     * @throws Throwable
     *
     * @return string
     */
    private function updateUser(InputInterface $input, OutputInterface $output, UserEntity $user): string
    {
        // Load entity to DTO
        $dtoLoaded = new UserDto();
        $dtoLoaded->load($user);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserDto $dtoEdit */
        $dtoEdit = $helper->interactUsingForm(UserType::class, $input, $output, ['data' => $dtoLoaded]);
        // Update user
        $this->userResource->update($user->getId(), $dtoEdit);

        return 'User updated - have a nice day';
    }
}
