<?php
declare(strict_types = 1);
/**
 * /src/Security/Interfaces/ApiKeyUser.php
 */

namespace App\Security\Interfaces;

use App\Entity\ApiKey;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @param array<int, string> $roles
     */
    public function __construct(ApiKey $apiKey, array $roles);

    /**
     * Getter method for ApiKey entity
     */
    public function getApiKey(): ApiKey;
}
