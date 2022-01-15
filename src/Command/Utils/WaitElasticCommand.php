<?php

declare(strict_types=1);

namespace App\Command\Utils;

use App\Command\Traits\SymfonyStyleTrait;
use App\Service\Interfaces\ElasticsearchServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class WaitElasticCommand
 *
 * @package App\Command\Utils
 */
class WaitElasticCommand extends Command
{
    use SymfonyStyleTrait;

    /**
     * Wait sleep time for elastic connection in seconds
     */
    private const WAIT_SLEEP_TIME = 2;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private ElasticsearchServiceInterface $elasticsearchService,
    ) {
        parent::__construct('elastic:wait');

        $this->setDescription('Waits for elastic availability.')
            ->setHelp('This command allows you to wait for elastic availability.');
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        for ($i = 0; $i < 120; $i += self::WAIT_SLEEP_TIME) {
            try {
                /** @var array<string, mixed> $data */
                $data = $this->elasticsearchService->info();
                $io->success('Connection to elastic ' . $data['version']['number'] . ' is ok!');

                return 0;
            } catch (Throwable) {
                $io->comment('Trying to connect to elastic seconds:' . $i);
                sleep(self::WAIT_SLEEP_TIME);
                $this->elasticsearchService->instantiate();

                continue;
            }
        }

        $io->error('Can not connect to elastic');

        return 1;
    }
}
