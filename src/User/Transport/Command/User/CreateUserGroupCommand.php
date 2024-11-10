<?php

declare(strict_types=1);

namespace App\User\Transport\Command\User;

use App\General\Transport\Command\HelperConfigure;
use App\General\Transport\Command\Traits\GetApplicationTrait;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Resource\RoleResource;
use App\User\Application\DTO\UserGroup\UserGroupCreate as UserGroupDto;
use App\User\Application\Resource\UserGroupResource;
use App\User\Transport\Form\Type\Console\UserGroupType;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @package App\User
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create user groups',
)]
class CreateUserGroupCommand extends Command
{
    use GetApplicationTrait;
    use SymfonyStyleTrait;

    final public const string NAME = 'user:create-group';

    /**
     * @var array<int, array<string, string>>
     */
    private static array $commandParameters = [
        [
            'name' => 'name',
            'description' => 'Name of the user group',
        ],
        [
            'name' => 'role',
            'description' => 'Role of the user group',
        ],
    ];

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly UserGroupResource $userGroupResource,
        private readonly RoleResource $roleResource,
    ) {
        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @throws InvalidArgumentException
     */
    #[Override]
    protected function configure(): void
    {
        parent::configure();

        HelperConfigure::configure($this, self::$commandParameters);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        // Check that roles exists
        $this->checkRoles($output, $input->isInteractive(), $io);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserGroupDto $dto */
        $dto = $helper->interactUsingForm(UserGroupType::class, $input, $output);
        // Create new user group
        $this->userGroupResource->create($dto);

        if ($input->isInteractive()) {
            $io->success('User group created - have a nice day');
        }

        return 0;
    }

    /**
     * Method to check if database contains role(s), if non exists method will run 'user:create-roles' command
     * which creates all roles to database so that user groups can be created.
     *
     * @throws Throwable
     */
    private function checkRoles(OutputInterface $output, bool $interactive, SymfonyStyle $io): void
    {
        if ($this->roleResource->getRepository()->countAdvanced() !== 0) {
            return;
        }

        if ($interactive) {
            $io->block('Roles are not yet created, creating those now...');
        }

        $command = $this->getApplication()->find('user:create-roles');
        $arguments = [
            'command' => 'user:create-roles',
        ];
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}
