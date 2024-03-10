<?php

declare(strict_types=1);

namespace App\Tool\Transport\Command\Elastic;

use App\General\Domain\Service\Interfaces\ElasticsearchServiceInterface;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_key_exists;

/**
 * @package App\Tool
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to create/update index template in Elastic.',
)]
class CreateOrUpdateTemplateCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'elastic:create-or-update-template';

    /**
     * Constructor
     *
     * @param \App\General\Infrastructure\Service\ElasticsearchService $elasticsearchService
     *
     * @throws LogicException
     */
    public function __construct(
        private readonly ElasticsearchServiceInterface $elasticsearchService,
        private readonly int $elasticNumberOfShards,
        private readonly int $elasticNumberOfReplicas,
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
