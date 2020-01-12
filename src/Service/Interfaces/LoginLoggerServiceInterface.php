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
     *
     * @param LogLoginResource $logLoginFailureResource
     * @param RequestStack     $requestStack
     */
    public function __construct(LogLoginResource $logLoginFailureResource, RequestStack $requestStack);

    /**
     * Setter for User object
     *
     * @param User|null $user
     *
     * @return LoginLoggerServiceInterface
     */
    public function setUser(?User $user = null): self;

    /**
     * Method to handle login event.
     *
     * @param string $type
     *
     * @throws Throwable
     */
    public function process(string $type): void;
}
