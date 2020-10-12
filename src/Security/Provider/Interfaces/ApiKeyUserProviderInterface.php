<?php
declare(strict_types = 1);
/**
 * /src/Security/Provider/Interfaces/ApiKeyUserProviderInterface.php
 */

namespace App\Security\Provider\Interfaces;

use App\Entity\ApiKey;
use App\Repository\ApiKeyRepository;
use App\Security\RolesService;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface ApiKeyUserProviderInterface
 *
 * @package App\Security\Provider\Interfaces
 */
interface ApiKeyUserProviderInterface extends UserProviderInterface
{
    /**
     * Constructor
     */
    public function __construct(ApiKeyRepository $apiKeyRepository, RolesService $rolesService);

    /**
     * Method to fetch ApiKey entity for specified token.
     */
    public function getApiKeyForToken(string $token): ?ApiKey;
}
