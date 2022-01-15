<?php

declare(strict_types=1);

namespace App\Command\Utils;

use App\Command\Traits\SymfonyStyleTrait;
use App\Repository\LogLoginRepository;
use App\Repository\LogRequestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class CleanupLogsCommand
 *
 * @package App\Command\Utils
 */
class CleanupLogsCommand extends Command
{
    use SymfonyStyleTrait;

    public const COMMAND_NAME = 'logs:cleanup';

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private LogLoginRepository $logLoginRepository,
        private LogRequestRepository $logRequestRepository,
    ) {
        parent::__construct(self::COMMAND_NAME);

        $this->setDescription('Command to cleanup logs(log_login, log_request) in the database');
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
        $result = $this->cleanUpDbTables();

        if ($result && $input->isInteractive()) {
            $io->success('Logs cleanup processed - have a nice day');
        }

        return 0;
    }

    /**
     * Cleanup db tables
     *
     * @throws Throwable
     */
    private function cleanUpDbTables(): bool
    {
        $this->logLoginRepository->cleanHistory();
        $this->logRequestRepository->cleanHistory();

        return true;
    }
}
