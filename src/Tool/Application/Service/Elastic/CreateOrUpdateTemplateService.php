<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Elastic;

use App\General\Domain\Service\Interfaces\ElasticsearchServiceInterface;
use App\Tool\Application\Service\Elastic\Interfaces\CreateOrUpdateTemplateServiceInterface;

use function array_key_exists;

/**
 * @package App\Tool
 */
class CreateOrUpdateTemplateService implements CreateOrUpdateTemplateServiceInterface
{
    public function __construct(
        private readonly ElasticsearchServiceInterface $elasticsearchService,
        private readonly int $elasticNumberOfShards,
        private readonly int $elasticNumberOfReplicas,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateIndexTemplate(): string
    {
        $action = 'Created';

        // get all templates
        $templates = $this->elasticsearchService->getTemplate([]);

        if (array_key_exists(ElasticsearchServiceInterface::TEMPLATE_NAME, $templates)) {
            $action = 'Updated';
        }

        $this->elasticsearchService->putTemplate([
            'name' => ElasticsearchServiceInterface::TEMPLATE_NAME,
            'body' => [
                'index_patterns' => [ElasticsearchServiceInterface::INDEX_PREFIX . '_*'],
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
                    ElasticsearchServiceInterface::INDEX_PREFIX => [
                        // required due to error in case empty
                        'filter' => [],
                    ],
                ],
            ],
        ]);

        return $action . ' ' . ElasticsearchServiceInterface::TEMPLATE_NAME . ' template';
    }
}
