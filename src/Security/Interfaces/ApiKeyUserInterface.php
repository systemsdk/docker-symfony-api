<?php
declare(strict_types = 1);
/**
 * /src/Security/Interfaces/ApiKeyUser.php
 */

namespace App\Security\Interfaces;

use App\Entity\ApiKey;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Security\RolesService;

/**
 * Interface ApiKeyUserInterface
 *
 * @package App\Security\Interfaces
 */
interface ApiKeyUserInterface extends UserInterface
{
    /**
     * Constructor
     *
     * @param ApiKey       $apiKey
     * @param RolesService $rolesService
     */
    public function __construct(ApiKey $apiKey, RolesService $rolesService);

    /**
     * Getter method for ApiKey entity
     *
     * @return ApiKey
     */
    public function getApiKey(): ApiKey;
}
