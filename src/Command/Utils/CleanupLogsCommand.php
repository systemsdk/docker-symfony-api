<?php
declare(strict_types = 1);
/**
 * /src/Command/Utils/CleanupLogsCommand.php
 */

namespace App\Command\Utils;

use App\Command\Traits\StyleSymfony;
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
    // Traits
    use StyleSymfony;

    public const COMMAND_NAME = 'logs:cleanup';
    private LogLoginRepository $logLoginRepository;
    private LogRequestRepository $logRequestRepository;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(LogLoginRepository $logLoginRepository, LogRequestRepository $logRequestRepository)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->logLoginRepository = $logLoginRepository;
        $this->logRequestRepository = $logRequestRepository;

        $this->setDescription('Command to cleanup logs(log_login, log_request) in the database');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
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
