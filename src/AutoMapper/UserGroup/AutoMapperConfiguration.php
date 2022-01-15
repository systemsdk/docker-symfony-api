<?php

declare(strict_types=1);

namespace App\AutoMapper\UserGroup;

use App\AutoMapper\RestAutoMapperConfiguration;
use App\DTO\UserGroup\UserGroupCreate;
use App\DTO\UserGroup\UserGroupPatch;
use App\DTO\UserGroup\UserGroupUpdate;

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
        UserGroupCreate::class,
        UserGroupUpdate::class,
        UserGroupPatch::class,
    ];

    public function __construct(
        protected RequestMapper $requestMapper,
    ) {
    }
}
