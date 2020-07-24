<?php
declare(strict_types = 1);
/**
 * /src/Service/ElasticsearchService.php
 */

namespace App\Service;

use App\Service\Interfaces\ElasticsearchServiceInterface;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;
use DateTimeInterface;

/**
 * Class ElasticsearchService
 *
 * @package App\Service
 */
class ElasticsearchService implements ElasticsearchServiceInterface
{
    public const INDEX_PREFIX = 'index';
    public const INDEX_DATE_FORMAT = 'Y_m_d';
    public const TEMPLATE_NAME = 'template_1';
    private Client $client;

    /**
     * Constructor
     *
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this->client = ClientBuilder::create()->setHosts([$host])
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function index(string $index, string $documentId, array $body)
    {
        return $this->client->index(['index' => $index, 'id' => $documentId, 'body' => $body]);
    }

    /**
     * @inheritDoc
     */
    public function info(array $params = [])
    {
        return $this->client->info($params);
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(array $params): array
    {
        return $this->client->indices()->getTemplate($params);
    }

    /**
     * @inheritDoc
     */
    public function putTemplate(array $params): array
    {
        return $this->client->indices()->putTemplate($params);
    }

    /**
     * @inheritDoc
     */
    public function search(string $index, array $body, int $from = 0, int $size = 100)
    {
        return $this->client->search(['index' => $index, 'from' => $from, 'size' => $size, 'body' => $body]);
    }

    /**
     * @inheritDoc
     */
    public static function generateIndexName(?int $timestamp = null): string
    {
        return self::INDEX_PREFIX . '_' .
            ($timestamp ? date(self::INDEX_DATE_FORMAT, $timestamp) : date(self::INDEX_DATE_FORMAT));
    }

    /**
     * @inheritDoc
     */
    public static function getDateTimeFormat(): string
    {
        return DateTimeInterface::RFC3339;
    }

    /**
     * @inheritDoc
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
