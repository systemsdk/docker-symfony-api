<?php

declare(strict_types=1);

namespace App\Tool\Transport\Command\Utils;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Tool\Application\Service\Utils\Interfaces\MessengerMessagesServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

/**
 * @package App\Tool
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to cleanup messenger_messages table.',
)]
class CleanupMessengerMessagesCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'messenger:messages-cleanup';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly MessengerMessagesServiceInterface $messengerMessagesService
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
        $result = $this->messengerMessagesService->cleanUp();

        if ($input->isInteractive()) {
            $io->success(sprintf('Messenger messages cleanup processed, deleted %s rows', $result));
        }

        return Command::SUCCESS;
    }
}
