<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\AutoMapper\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKeyCreate;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyPatch;
use App\ApiKey\Application\DTO\ApiKey\ApiKeyUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

/**
 * @package App\ApiKey
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    /**
     * Classes to use specified request mapper.
     *
     * @var array<int, class-string>
     */
    protected static array $requestMapperClasses = [
        ApiKeyCreate::class,
        ApiKeyUpdate::class,
        ApiKeyPatch::class,
    ];

    public function __construct(
        RequestMapper $requestMapper,
    ) {
        parent::__construct($requestMapper);
    }
}
