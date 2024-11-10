<?php

declare(strict_types=1);

namespace App\Role\Transport\Command\Role;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Service\Role\Interfaces\SyncRolesServiceInterface;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

/**
 * @package App\Role
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create roles to database',
)]
class CreateRolesCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'user:create-roles';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly SyncRolesServiceInterface $syncRolesService,
    ) {
        parent::__construct();
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
        $data = $this->syncRolesService->syncRoles();

        if ($input->isInteractive()) {
            $message = sprintf(
                'Created total of %d role(s) and removed %d role(s) - have a nice day',
                $data['created'],
                $data['removed'],
            );
            $io->success($message);
        }

        return Command::SUCCESS;
    }
}
