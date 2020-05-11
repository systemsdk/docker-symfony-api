<?php
declare(strict_types = 1);
/**
 * /src/Command/Scheduler/CleanupLogsScheduledCommand.php
 */

namespace App\Command\Scheduler;

use App\Command\Traits\StyleSymfony;
use App\Command\Utils\CleanupLogsCommand;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Exception\LogicException;
use Throwable;

/**
 * Class CleanupLogsScheduledCommand
 *
 * @package App\Command\Scheduler
 */
class CleanupLogsScheduledCommand extends Command
{
    // Traits
    use StyleSymfony;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     *
     * @throws LogicException
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('scheduler:cleanup-logs');

        $this->entityManager = $entityManager;

        $this->setDescription(
            'Command to run a cron job for cleanup logs by schedule.'
        );
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);

        $message = $this->createScheduledCommand($input, $output);

        if ($input->isInteractive()) {
            $io->success($message);
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return string
     */
    private function createScheduledCommand(
        InputInterface $input,
        OutputInterface $output
    ): string {
        $entity = $this->entityManager->getRepository(ScheduledCommand::class)->findOneBy([
            'command' => CleanupLogsCommand::COMMAND_NAME,
        ]);

        if ($entity !== null) {
            return "The job CleanupLogs is already present [id='{$entity->getId()}'] - have a nice day";
        }

        # ┌───────────── minute (0 - 59)
        # | ┌───────────── hour (0 - 23)
        # │ │ ┌───────────── day of the month (1 - 31)
        # │ │ │ ┌───────────── month (1 - 12)
        # │ │ │ │ ┌───────────── day of the week (0 - 6) (Sunday to Saturday;
        # │ │ │ │ │                                   7 is also Sunday on some systems)
        # │ │ │ │ │
        # │ │ │ │ │
        # * * * * * command to execute

        $scheduledCommand = (new ScheduledCommand())
            ->setName('Cleanup logs in tables log_login, log_request')
            ->setCommand(CleanupLogsCommand::COMMAND_NAME)
            ->setCronExpression('0 0 * * *')  // Run once a day, midnight
            ->setPriority(100)
            ->setLogFile('cleanup-logs.log')
            ->setExecuteImmediately(false)
            ->setDisabled(false);

        $this->entityManager->persist($scheduledCommand);
        $this->entityManager->flush();

        return 'The job CleanupLogs is created - have a nice day';
    }
}
