<?php

declare(strict_types=1);

namespace App\Tool\Transport\Command\Scheduler;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Tool\Application\Service\Scheduler\Interfaces\ScheduledCommandServiceInterface;
use App\Tool\Transport\Command\Utils\CleanupMessengerMessagesCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\Tool
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to create a cron job for cleanup messenger_messages table by schedule.',
)]
class CleanupMessengerMessagesScheduledCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'scheduler:cleanup-messenger-messages';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly ScheduledCommandServiceInterface $scheduledCommandService,
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
        $message = $this->process();

        if ($input->isInteractive()) {
            $io->success($message);
        }

        return Command::SUCCESS;
    }

    /**
     * @throws Throwable
     */
    private function process(): string
    {
        $entity = $this->scheduledCommandService->findByCommand(CleanupMessengerMessagesCommand::NAME);

        if ($entity) {
            return "The job CleanupMessengerMessages is already present [id='{$entity->getId()}']";
        }

        // ┌───────────── minute (0 - 59)
        // | ┌───────────── hour (0 - 23)
        // │ │ ┌───────────── day of the month (1 - 31)
        // │ │ │ ┌───────────── month (1 - 12)
        // │ │ │ │ ┌───────────── day of the week (0 - 6) (Sunday to Saturday; 7 is also Sunday on some systems)
        // │ │ │ │ │
        // * * * * * command to execute
        // Run once a day, 00:00
        $cronExpression = '0 0 * * *';
        $this->scheduledCommandService->create(
            'Cleanup table messenger_messages',
            CleanupMessengerMessagesCommand::NAME,
            $cronExpression,
            '/cleanup-messenger-messages.log'
        );

        return 'The job CleanupMessengerMessages is created';
    }
}
