<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Service;

use App\General\Domain\Service\Interfaces\ElasticsearchServiceInterface;
use DateTimeInterface;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 * @package App\General
 */
class ElasticsearchService implements ElasticsearchServiceInterface
{
    private Client $client;

    public function __construct(
        private readonly string $host,
        private readonly string $username,
        private readonly string $password,
    ) {
        $this->instantiate();
    }

    /**
     * {@inheritDoc}
     */
    public function instantiate(): void
    {
        $this->client = ClientBuilder::create()->setHosts([$this->host])
            ->setBasicAuthentication($this->username, $this->password)
            ->build();
    }

    /**
     * {@inheritDoc}
     */
    public function index(string $index, string $documentId, array $body): mixed
    {
        return $this->client->index([
            'index' => $index,
            'id' => $documentId,
            'body' => $body,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function info(array $params = []): mixed
    {
        return $this->client->info($params);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplate(array $params): array
    {
        return $this->client->indices()->getTemplate($params);
    }

    /**
     * {@inheritDoc}
     */
    public function putTemplate(array $params): array
    {
        return $this->client->indices()->putTemplate($params);
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $index, array $body, int $from = 0, int $size = 100): mixed
    {
        return $this->client->search(
            [
                'index' => $index,
                'from' => $from,
                'size' => $size,
                'body' => $body,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function generateIndexName(?int $timestamp = null): string
    {
        $date = $timestamp
            ? date(ElasticsearchServiceInterface::INDEX_DATE_FORMAT, $timestamp)
            : date(ElasticsearchServiceInterface::INDEX_DATE_FORMAT);

        return self::INDEX_PREFIX . '_' . $date;
    }

    /**
     * {@inheritDoc}
     */
    public static function getDateTimeFormat(): string
    {
        return DateTimeInterface::RFC3339;
    }

    /**
     * {@inheritDoc}
     */
    public static function getPropertiesData(): array
    {
        return [
            'row_hash' => [
                'type' => 'text',
            ],
            'time_local' => [
                'type' => 'date',
            ],
            // TODO: extend/edit elastic properties according to your needs
        ];
    }
}
