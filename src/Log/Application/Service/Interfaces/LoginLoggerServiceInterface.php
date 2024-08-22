<?php

declare(strict_types=1);

namespace App\Log\Application\Service\Interfaces;

use App\Log\Application\Resource\LogLoginResource;
use App\Log\Domain\Enum\LogLogin;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * @package App\Log
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
    public function process(LogLogin $type): void;
}
