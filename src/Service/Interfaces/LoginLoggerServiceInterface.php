<?php
declare(strict_types = 1);
/**
 * /src/Service/Interfaces/LoginLoggerServiceInterface.php
 */

namespace App\Service\Interfaces;

use App\Entity\User;
use App\Resource\LogLoginResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * Interface LoginLoggerService
 *
 * @package App\Service\Interfaces
 */
interface LoginLoggerServiceInterface
{
    /**
     * Constructor
     */
    public function __construct(LogLoginResource $logLoginFailureResource, RequestStack $requestStack);

    /**
     * Setter for User object (Entity).
     */
    public function setUser(?User $user = null): self;

    /**
     * Method to handle login event.
     *
     * @throws Throwable
     */
    public function process(string $type): void;
}
