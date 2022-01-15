<?php

declare(strict_types=1);

namespace App\Security\Interfaces;

use App\Entity\ApiKey;

/**
 * Interface ApiKeyUserInterface
 *
 * @package App\Security\Interfaces
 */
interface ApiKeyUserInterface
{
    /**
     * Constructor
     *
     * @param array<int, string> $roles
     */
    public function __construct(ApiKey $apiKey, array $roles);
}
