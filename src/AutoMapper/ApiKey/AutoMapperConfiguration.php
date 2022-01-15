<?php

declare(strict_types=1);

namespace App\AutoMapper\ApiKey;

use App\AutoMapper\RestAutoMapperConfiguration;
use App\DTO\ApiKey\ApiKeyCreate;
use App\DTO\ApiKey\ApiKeyPatch;
use App\DTO\ApiKey\ApiKeyUpdate;

/**
 * Class AutoMapperConfiguration
 *
 * @package App\AutoMapper
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
        protected RequestMapper $requestMapper,
    ) {
    }
}
