<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Command\Traits\ApiKeyUserManagementHelperTrait;
use App\Command\Traits\SymfonyStyleTrait;
use App\Repository\RoleRepository;
use App\Resource\UserGroupResource;
use App\Security\RolesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Class CreateRolesWithUserGroupsCommand
 *
 * @package App\Command\User
 */
class CreateRolesWithUserGroupsCommand extends Command
{
    // Traits
    use ApiKeyUserManagementHelperTrait;
    use SymfonyStyleTrait;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private UserGroupResource $userGroupResource,
        private RolesService $rolesService,
        private RoleRepository $roleRepository,
    ) {
        parent::__construct('user:create-roles-groups');

        $this->setDescription('Console command to create roles with user groups to database');
    }

    /**
     * Getter for RolesService
     */
    public function getRolesService(): RolesService
    {
        return $this->rolesService;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        // Check that roles exists
        $result = $this->checkUserGroups($output, $input->isInteractive(), $io);

        if ($result && $input->isInteractive()) {
            $io->success('Roles with user groups processed - have a nice day');
        }

        return 0;
    }

    /**
     * Method to check if database contains user groups, if non exists method will run 'user:create-group' command
     * to create those automatically according to '$this->roles->getRoles()' output. Basically this will automatically
     * create user groups for each role that is defined to application.
     *
     * Also note that if groups are not found method will reset application 'role' table content, so that we can be
     * sure that we can create all groups correctly.
     *
     * @throws Throwable
     */
    private function checkUserGroups(OutputInterface $output, bool $interactive, SymfonyStyle $io): bool
    {
        if ($this->userGroupResource->count() !== 0) {
            $io->warning('User groups already created, stop processing.');

            return false;
        }

        if ($interactive) {
            $io->block('User groups are not yet created, creating those now...');
        }

        // Reset roles
        $this->roleRepository->reset();
        // Create user groups for each role
        $this->createUserGroups($output);

        return true;
    }
}
