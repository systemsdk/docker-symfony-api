<?php

declare(strict_types=1);

namespace App\Security\Provider\Interfaces;

use App\Entity\ApiKey;
use App\Repository\ApiKeyRepository;
use App\Security\RolesService;

/**
 * Interface ApiKeyUserProviderInterface
 *
 * @package App\Security\Provider\Interfaces
 */
interface ApiKeyUserProviderInterface
{
    public function __construct(ApiKeyRepository $apiKeyRepository, RolesService $rolesService);

    /**
     * Method to fetch ApiKey entity for specified token.
     */
    public function getApiKeyForToken(string $token): ?ApiKey;
}
