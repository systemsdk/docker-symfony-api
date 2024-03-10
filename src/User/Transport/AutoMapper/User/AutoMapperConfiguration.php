<?php

declare(strict_types=1);

namespace App\User\Transport\AutoMapper\User;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\User\Application\DTO\User\UserCreate;
use App\User\Application\DTO\User\UserPatch;
use App\User\Application\DTO\User\UserUpdate;

/**
 * @package App\User
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
        RequestMapper $requestMapper,
    ) {
        parent::__construct($requestMapper);
    }
}
