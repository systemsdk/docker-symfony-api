<?php

declare(strict_types=1);

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
     * @return callable|array
     */
    public function index(string $index, string $documentId, array $body);

    /**
     * Get info about elastic
     *
     * @return callable|array
     */
    public function info(array $params = []);

    public function getTemplate(array $params): array;

    /**
     * Create/update template
     * https://www.elastic.co/guide/en/elasticsearch/reference/master/indices-templates-v1.html
     */
    public function putTemplate(array $params): array;

    /**
     * Search for a elastic document
     *
     * @return callable|array
     */
    public function search(string $index, array $body, int $from, int $size);

    /**
     * Create string with index name
     */
    public static function generateIndexName(?int $timestamp = null): string;

    /**
     * Get elastic properties types, etc...
     */
    public static function getPropertiesData(): array;

    /**
     * Get format for datetime to text transformation
     */
    public static function getDateTimeFormat(): string;
}
