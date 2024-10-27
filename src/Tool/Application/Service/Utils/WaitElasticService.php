<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils;

use App\General\Domain\Service\Interfaces\ElasticsearchServiceInterface;
use App\Tool\Application\Service\Utils\Interfaces\WaitElasticServiceInterface;

/**
 * @package App\Tool
 */
class WaitElasticService implements WaitElasticServiceInterface
{
    public function __construct(
        private readonly ElasticsearchServiceInterface $elasticsearchService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(): mixed
    {
        $this->elasticsearchService->instantiate();

        return $this->elasticsearchService->info();
    }
}
