<?php
declare(strict_types = 1);
/**
 * /src/Command/Traits/ExecuteMultipleCommand.php
 */

namespace App\Command\Traits;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Throwable;

/**
 * Trait ExecuteMultipleCommand
 *
 * @package App\Command\Traits
 */
trait ExecuteMultipleCommand
{
    // Traits
    use GetApplication;

    private array $choices = [];
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private SymfonyStyle $io;

    /**
     * Setter method for choices to use.
     *
     * @param array $choices
     */
    protected function setChoices(array $choices): void
    {
        $this->choices = $choices;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->write("\033\143");

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($command = $this->ask()) {
            $arguments = [
                'command' => $command,
            ];
            $input = new ArrayInput($arguments);
            $cmd = $this->getApplication()->find((string)$command);
            $cmd->run($input, $output);
        }

        if ($input->isInteractive()) {
            $this->io->success('Have a nice day');
        }

        return 0;
    }

    /**
     * Method to ask user to make choose one of defined choices.
     *
     * @return string|bool
     */
    private function ask()
    {
        $index = array_search(
            $this->io->choice('What you want to do', array_values($this->choices)),
            array_values($this->choices),
            true
        );
        $choice = (string)array_values(array_flip($this->choices))[(int)$index];

        return $choice === '0' ? false : $choice;
    }
}
