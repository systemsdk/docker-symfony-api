<?php

declare(strict_types=1);

namespace App\User\Transport\Command\User;

use App\General\Transport\Command\HelperConfigure;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Resource\RoleResource;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\DTO\User\UserCreate as UserDto;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Transport\Command\Traits\ApiKeyUserManagementHelperTrait;
use App\User\Transport\Form\Type\Console\UserType;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @package App\User
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create user to database',
)]
class CreateUserCommand extends Command
{
    use ApiKeyUserManagementHelperTrait;
    use SymfonyStyleTrait;

    final public const string NAME = 'user:create';
    private const string PARAMETER_NAME = 'name';
    private const string PARAMETER_DESCRIPTION = 'description';

    /**
     * @var array<int, array<string, string>>
     */
    private static array $commandParameters = [
        [
            self::PARAMETER_NAME => 'username',
            self::PARAMETER_DESCRIPTION => 'Username',
        ],
        [
            self::PARAMETER_NAME => 'firstName',
            self::PARAMETER_DESCRIPTION => 'First name of the user',
        ],
        [
            self::PARAMETER_NAME => 'lastName',
            self::PARAMETER_DESCRIPTION => 'Last name of the user',
        ],
        [
            self::PARAMETER_NAME => 'email',
            self::PARAMETER_DESCRIPTION => 'Email of the user',
        ],
        [
            self::PARAMETER_NAME => 'plainPassword',
            self::PARAMETER_DESCRIPTION => 'Plain password for user',
        ],
        [
            self::PARAMETER_NAME => 'userGroups',
            self::PARAMETER_DESCRIPTION => 'User groups where to attach user',
        ],
    ];

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly UserResource $userResource,
        private readonly UserGroupResource $userGroupResource,
        private readonly RoleResource $roleResource,
        private readonly RolesServiceInterface $rolesService,
    ) {
        parent::__construct();
    }

    #[Override]
    public function getRolesService(): RolesServiceInterface
    {
        return $this->rolesService;
    }

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
        $this->checkUserGroups($output, $input->isInteractive(), $io);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserDto $dto */
        $dto = $helper->interactUsingForm(UserType::class, $input, $output);
        // Create new user
        $this->userResource->create($dto);

        if ($input->isInteractive()) {
            $io->success('User created - have a nice day');
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
    private function checkUserGroups(OutputInterface $output, bool $interactive, SymfonyStyle $io): void
    {
        if ($this->userGroupResource->count() !== 0) {
            return;
        }

        if ($interactive) {
            $io->block('User groups are not yet created, creating those now...');
        }

        // Reset roles
        $this->roleResource->getRepository()->reset();
        // Create user groups for each role
        $this->createUserGroups($output);
    }
}
