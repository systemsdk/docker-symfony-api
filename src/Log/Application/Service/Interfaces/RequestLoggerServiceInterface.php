<?php

declare(strict_types=1);

namespace App\Log\Application\Service\Interfaces;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\Log\Application\Resource\LogRequestResource;
use App\User\Application\Resource\UserResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Log
 */
interface RequestLoggerServiceInterface
{
    /**
     * @param array<int, string> $sensitiveProperties
     */
    public function __construct(
        LogRequestResource $logRequestResource,
        UserResource $userResource,
        ApiKeyResource $apiKeyResource,
        LoggerInterface $logger,
        array $sensitiveProperties,
    );

    /**
     * Setter for response object.
     */
    public function setResponse(Response $response): self;

    /**
     * Setter for request object.
     */
    public function setRequest(Request $request): self;

    /**
     * Setter method for current user.
     */
    public function setUserId(string $userId): self;

    /**
     * Setter method for current api key
     */
    public function setApiKeyId(string $apiKeyId): self;

    /**
     * Setter method for 'main request' info.
     */
    public function setMainRequest(bool $mainRequest): self;

    /**
     * Method to handle current response and log it to database.
     */
    public function handle(): void;
}
