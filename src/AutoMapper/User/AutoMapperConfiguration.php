<?php

declare(strict_types=1);

namespace App\AutoMapper\User;

use App\AutoMapper\RestAutoMapperConfiguration;
use App\DTO\User\UserCreate;
use App\DTO\User\UserPatch;
use App\DTO\User\UserUpdate;

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
        UserCreate::class,
        UserUpdate::class,
        UserPatch::class,
    ];

    public function __construct(
        protected RequestMapper $requestMapper,
    ) {
    }
}
