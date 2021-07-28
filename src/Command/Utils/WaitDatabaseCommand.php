<?php

declare(strict_types=1);

namespace App\Command\Utils;

use App\Command\Traits\SymfonyStyleTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class WaitDatabaseCommand
 *
 * @package App\Command\Utils
 */
class WaitDatabaseCommand extends Command
{
    use SymfonyStyleTrait;

    /**
     * Wait sleep time for db connection in seconds
     */
    private const WAIT_SLEEP_TIME = 2;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct('db:wait');

        $this->setDescription('Waits for database availability.')
            ->setHelp('This command allows you to wait for database availability.');
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * Execute the console command.
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        for ($i = 0; $i < 60; $i += self::WAIT_SLEEP_TIME) {
            try {
                $connection = $this->em->getConnection();
                $statement = $connection->prepare('SHOW TABLES');
                $statement->execute();
                $io->success('Connection to the database is ok!');

                return 0;
            } catch (Throwable) {
                $io->comment('Trying to connect to the database seconds:' . $i);
                sleep(self::WAIT_SLEEP_TIME);

                continue;
            }
        }

        $io->error('Can not connect to the database');

        return 1;
    }
}
