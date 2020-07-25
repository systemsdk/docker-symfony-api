<?php
declare(strict_types = 1);
/**
 * /src/Service/Interfaces/ElasticsearchServiceInterface.php
 */

namespace App\Service\Interfaces;

/**
 * Interface ElasticsearchServiceInterface
 *
 * @package App\Service\Interfaces
 */
interface ElasticsearchServiceInterface
{
    /**
     * Instantiate client instance
     */
    public function instantiate(): void;

    /**
     * Index a document
     *
     * @param string $index
     * @param string $documentId
     * @param array $body
     *
     * @return callable|array
     */
    public function index(string $index, string $documentId, array $body);

    /**
     * Get info about elastic
     *
     * @param array $params
     *
     * @return callable|array
     */
    public function info(array $params = []);

    /**
     * Get template
     *
     * @param array $params
     *
     * @return array
     */
    public function getTemplate(array $params): array;

    /**
     * Create/update template
     * https://www.elastic.co/guide/en/elasticsearch/reference/master/indices-templates-v1.html
     *
     * @param array $params
     *
     * @return array
     */
    public function putTemplate(array $params): array;

    /**
     * Search for a elastic document
     *
     * @param string $index
     * @param array $body
     * @param int $from
     * @param int $size
     *
     * @return callable|array
     */
    public function search(string $index, array $body, int $from, int $size);

    /**
     * Create string with index name
     *
     * @param int|null $timestamp
     *
     * @return string
     */
    public static function generateIndexName(?int $timestamp = null): string;

    /**
     * Get elastic properties types, etc...
     *
     * @return array
     */
    public static function getPropertiesData(): array;

    /**
     * Get format for datetime to text transformation
     *
     * @return string
     */
    public static function getDateTimeFormat(): string;
}
