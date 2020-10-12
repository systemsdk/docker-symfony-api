<?php
declare(strict_types = 1);
/**
 * /src/Command/Utils/WaitElasticCommand.php
 */

namespace App\Command\Utils;

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
    /**
     * Wait sleep time for elastic connection in seconds
     */
    private const WAIT_SLEEP_TIME = 2;
    private ElasticsearchServiceInterface $elasticsearchService;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(ElasticsearchServiceInterface $elasticsearchService)
    {
        parent::__construct('elastic:wait');

        $this->elasticsearchService = $elasticsearchService;
        $this->setDescription('Waits for elastic availability.')
            ->setHelp('This command allows you to wait for elastic availability.');
    }

    /**
     * Execute the console command.
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 0; $i < 120; $i += self::WAIT_SLEEP_TIME) {
            try {
                $data = $this->elasticsearchService->info();
                $output->writeln('<info>Connection to elastic ' . $data['version']['number'] . ' is ok!</info>');

                return 0;
            } catch (Throwable $exception) {
                $output->writeln('<comment>Trying to connect to elastic seconds:' . $i . '</comment>');
                sleep(self::WAIT_SLEEP_TIME);
                $this->elasticsearchService->instantiate();

                continue;
            }
        }

        $output->writeln('<error>Can not connect to elastic</error>');

        return 1;
    }
}
