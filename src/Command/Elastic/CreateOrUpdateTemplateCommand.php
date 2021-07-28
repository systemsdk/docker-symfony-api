<?php

declare(strict_types=1);

namespace App\Command\Elastic;

use App\Command\Traits\SymfonyStyleTrait;
use App\Service\Interfaces\ElasticsearchServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_key_exists;

/**
 * Class CreateOrUpdateTemplateCommand
 *
 * @package App\Command\Elastic
 */
class CreateOrUpdateTemplateCommand extends Command
{
    use SymfonyStyleTrait;

    public const COMMAND_NAME = 'elastic:create-or-update-template';

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private ElasticsearchServiceInterface $elasticsearchService,
        private int $elasticNumberOfShards,
        private int $elasticNumberOfReplicas,
    ) {
        parent::__construct(self::COMMAND_NAME);

        $this->setDescription('Command to create/update index template in Elastic');
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
        $message = $this->createIndexTemplate();

        if ($input->isInteractive()) {
            $io->success($message);
        }

        return 0;
    }

    /**
     * Create/update elastic template
     *
     * @throws Throwable
     */
    private function createIndexTemplate(): string
    {
        $action = 'Created';

        // get all templates
        $templates = $this->elasticsearchService->getTemplate([]);

        if (array_key_exists($this->elasticsearchService::TEMPLATE_NAME, $templates)) {
            $action = 'Updated';
        }

        $this->elasticsearchService->putTemplate([
            'name' => $this->elasticsearchService::TEMPLATE_NAME,
            'body' => [
                'index_patterns' => [$this->elasticsearchService::INDEX_PREFIX . '_*'],
                'settings' => [
                    'number_of_shards' => $this->elasticNumberOfShards,
                    'number_of_replicas' => $this->elasticNumberOfReplicas,
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true,
                    ],
                    'properties' => $this->elasticsearchService::getPropertiesData(),
                ],
                'aliases' => [
                    $this->elasticsearchService::INDEX_PREFIX => [
                        // required due to error in case empty
                        'filter' => [],
                    ],
                ],
            ],
        ]);

        return $action . ' ' . $this->elasticsearchService::TEMPLATE_NAME . ' template - have a nice day';
    }
}
