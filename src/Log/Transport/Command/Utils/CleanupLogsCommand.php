<?php

declare(strict_types=1);

namespace App\Log\Transport\Command\Utils;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Log\Application\Service\Utils\Interfaces\CleanupLogServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\Log
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to cleanup logs(log_login, log_request) in the database.',
)]
class CleanupLogsCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'logs:cleanup';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly CleanupLogServiceInterface $cleanupLogService
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        $result = $this->cleanupLogService->cleanup();

        if ($result && $input->isInteractive()) {
            $io->success('Logs cleanup processed - have a nice day');
        }

        return Command::SUCCESS;
    }
}
