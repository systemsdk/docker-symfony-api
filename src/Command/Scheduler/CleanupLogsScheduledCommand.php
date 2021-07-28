<?php

declare(strict_types=1);

namespace App\Command\Scheduler;

use App\Command\Traits\SymfonyStyleTrait;
use App\Command\Utils\CleanupLogsCommand;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class CleanupLogsScheduledCommand
 *
 * @package App\Command\Scheduler
 */
class CleanupLogsScheduledCommand extends Command
{
    use SymfonyStyleTrait;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct('scheduler:cleanup-logs');

        $this->setDescription(
            'Command to run a cron job for cleanup logs by schedule.'
        );
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * Executes the current command.
     *
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);

        $message = $this->createScheduledCommand();

        if ($input->isInteractive()) {
            $io->success($message);
        }

        return 0;
    }

    /**
     * @throws Throwable
     */
    private function createScheduledCommand(): string
    {
        $entity = $this->entityManager->getRepository(ScheduledCommand::class)->findOneBy([
            'command' => CleanupLogsCommand::COMMAND_NAME,
        ]);

        if ($entity !== null) {
            return "The job CleanupLogs is already present [id='{$entity->getId()}'] - have a nice day";
        }

        // ┌───────────── minute (0 - 59)
        // | ┌───────────── hour (0 - 23)
        // │ │ ┌───────────── day of the month (1 - 31)
        // │ │ │ ┌───────────── month (1 - 12)
        // │ │ │ │ ┌───────────── day of the week (0 - 6) (Sunday to Saturday;
        // │ │ │ │ │                                   7 is also Sunday on some systems)
        // │ │ │ │ │
        // │ │ │ │ │
        // * * * * * command to execute

        $scheduledCommand = (new ScheduledCommand())
            ->setName('Cleanup logs in tables log_login, log_request')
            ->setCommand(CleanupLogsCommand::COMMAND_NAME)
            // Run once a day, midnight
            ->setCronExpression('0 0 * * *')
            ->setPriority(100)
            ->setLastExecution(new DateTime())
            ->setLogFile('/cleanup-logs.log')
            ->setExecuteImmediately(false)
            ->setDisabled(false);

        $this->entityManager->persist($scheduledCommand);
        $this->entityManager->flush();

        return 'The job CleanupLogs is created - have a nice day';
    }
}
