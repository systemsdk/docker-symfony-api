<?php

declare(strict_types=1);

namespace App\Role\Transport\Command\Role;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Resource\RoleResource;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Resource\UserGroupResource;
use App\User\Transport\Command\Traits\ApiKeyUserManagementHelperTrait;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @package App\Role
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create roles with user groups to database',
)]
class CreateRolesWithUserGroupsCommand extends Command
{
    use ApiKeyUserManagementHelperTrait;
    use SymfonyStyleTrait;

    final public const string NAME = 'user:create-roles-groups';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly UserGroupResource $userGroupResource,
        private readonly RoleResource $roleResource,
        private readonly RolesServiceInterface $rolesService,
    ) {
        parent::__construct();
    }

    /**
     * Getter for RolesService
     */
    #[Override]
    public function getRolesService(): RolesServiceInterface
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

        return Command::SUCCESS;
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
        $this->roleResource->getRepository()->reset();
        // Create user groups for each role
        $this->createUserGroups($output);

        return true;
    }
}
