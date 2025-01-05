<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Command\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate as ApiKey;
use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Transport\Form\Type\Console\ApiKeyType;
use App\General\Transport\Command\HelperConfigure;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Resource\RoleResource;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Resource\UserGroupResource;
use App\User\Transport\Command\Traits\ApiKeyUserManagementHelperTrait;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @package App\ApiKey
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to create new API key',
)]
class CreateApiKeyCommand extends Command
{
    use ApiKeyUserManagementHelperTrait;
    use SymfonyStyleTrait;

    final public const string NAME = 'api-key:create';

    /**
     * @var array<int, array<string, string>>
     */
    private static array $commandParameters = [
        [
            'name' => 'description',
            'description' => 'Description',
        ],
    ];

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly ApiKeyHelper $apiKeyHelper,
        private readonly ApiKeyResource $apiKeyResource,
        private readonly UserGroupResource $userGroupResource,
        private readonly RolesServiceInterface $rolesService,
        private readonly RoleResource $roleResource,
    ) {
        parent::__construct();
    }

    #[Override]
    public function getRolesService(): RolesServiceInterface
    {
        return $this->rolesService;
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

        // Check that user group(s) exists
        $this->checkUserGroups($io, $output, $input->isInteractive());
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var ApiKey $dto */
        $dto = $helper->interactUsingForm(ApiKeyType::class, $input, $output);
        // Create new API key
        $apiKey = $this->apiKeyResource->create($dto);

        if ($input->isInteractive()) {
            $io->success($this->apiKeyHelper->getApiKeyMessage('API key created - have a nice day', $apiKey));
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
    private function checkUserGroups(SymfonyStyle $io, OutputInterface $output, bool $interactive): void
    {
        if ($this->userGroupResource->count() !== 0) {
            return;
        }

        if ($interactive) {
            $io->block(['User groups are not yet created, creating those now...']);
        }

        // Reset roles
        $this->roleResource->getRepository()->reset();
        // Create user groups for each roles
        $this->createUserGroups($output);
    }
}
