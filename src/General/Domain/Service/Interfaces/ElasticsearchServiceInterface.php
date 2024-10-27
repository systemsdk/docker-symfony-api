<?php

declare(strict_types=1);

namespace App\General\Domain\Service\Interfaces;

/**
 * @package App\General
 */
interface ElasticsearchServiceInterface
{
    final public const string INDEX_PREFIX = 'index';
    final public const string INDEX_DATE_FORMAT = 'Y_m_d';
    final public const string TEMPLATE_NAME = 'template_1';

    /**
     * Instantiate client instance
     */
    public function instantiate(): void;

    /**
     * Index a document
     *
     * @param array<int|string, mixed> $body
     *
     * @return callable|array<int|string, mixed>
     */
    public function index(string $index, string $documentId, array $body): mixed;

    /**
     * Get info about elastic
     *
     * @param array<int|string, mixed> $params
     *
     * @return callable|array<int|string, mixed>
     */
    public function info(array $params = []): mixed;

    /**
     * @param array<string, mixed> $params
     *
     * @return array<int|string, mixed>
     */
    public function getTemplate(array $params): array;

    /**
     * Create/update template
     * https://www.elastic.co/guide/en/elasticsearch/reference/master/indices-templates-v1.html
     *
     * @param array<string, mixed> $params
     *
     * @return array<int|string, mixed>
     */
    public function putTemplate(array $params): array;

    /**
     * Search for a elastic document
     *
     * @param array<int|string, mixed> $body
     *
     * @return callable|array<int|string, mixed>
     */
    public function search(string $index, array $body, int $from, int $size): mixed;

    /**
     * Create string with index name
     */
    public static function generateIndexName(?int $timestamp = null): string;

    /**
     * Get elastic properties types, etc...
     *
     * @return array<int|string, mixed>
     */
    public static function getPropertiesData(): array;

    /**
     * Get format for datetime to text transformation
     */
    public static function getDateTimeFormat(): string;
}
