<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

use App\Entity\User;
use App\Resource\LogLoginResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * Interface LoginLoggerServiceInterface
 *
 * @package App\Service\Interfaces
 */
interface LoginLoggerServiceInterface
{
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
