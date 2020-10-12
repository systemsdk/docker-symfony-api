<?php
declare(strict_types = 1);
/**
 * /src/Service/ElasticsearchService.php
 */

namespace App\Service;

use App\Service\Interfaces\ElasticsearchServiceInterface;
use DateTimeInterface;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

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

    private string $host;
    private string $username;
    private string $password;
    private Client $client;

    /**
     * Constructor
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->instantiate();
    }

    /**
     * {@inheritdoc}
     */
    public function instantiate(): void
    {
        $this->client = ClientBuilder::create()->setHosts([$this->host])
            ->setBasicAuthentication($this->username, $this->password)
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function index(string $index, string $documentId, array $body)
    {
        return $this->client->index(['index' => $index, 'id' => $documentId, 'body' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public function info(array $params = [])
    {
        return $this->client->info($params);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(array $params): array
    {
        return $this->client->indices()->getTemplate($params);
    }

    /**
     * {@inheritdoc}
     */
    public function putTemplate(array $params): array
    {
        return $this->client->indices()->putTemplate($params);
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $index, array $body, int $from = 0, int $size = 100)
    {
        return $this->client->search(['index' => $index, 'from' => $from, 'size' => $size, 'body' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public static function generateIndexName(?int $timestamp = null): string
    {
        return self::INDEX_PREFIX . '_' .
            ($timestamp ? date(self::INDEX_DATE_FORMAT, $timestamp) : date(self::INDEX_DATE_FORMAT));
    }

    /**
     * {@inheritdoc}
     */
    public static function getDateTimeFormat(): string
    {
        return DateTimeInterface::RFC3339;
    }

    /**
     * {@inheritdoc}
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
