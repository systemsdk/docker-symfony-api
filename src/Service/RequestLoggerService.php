<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\LogRequest;
use App\Resource\ApiKeyResource;
use App\Resource\LogRequestResource;
use App\Resource\UserResource;
use App\Service\Interfaces\RequestLoggerServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class RequestLoggerService
 *
 * @package App\Service
 */
class RequestLoggerService implements RequestLoggerServiceInterface
{
    private ?Response $response = null;
    private ?Request $request = null;
    private ?string $userId = null;
    private ?string $apiKeyId = null;
    private bool $mainRequest = false;

    /**
     * Constructor
     *
     * @param array<int, string> $sensitiveProperties
     */
    public function __construct(
        private LogRequestResource $logRequestResource,
        private UserResource $userResource,
        private ApiKeyResource $apiKeyResource,
        private LoggerInterface $logger,
        private array $sensitiveProperties,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKeyId(string $apiKeyId): self
    {
        $this->apiKeyId = $apiKeyId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainRequest(bool $mainRequest): self
    {
        $this->mainRequest = $mainRequest;

        return $this;
    }

    /**
     * Method to handle current response and log it to database.
     */
    public function handle(): void
    {
        // Just check that we have all that we need
        if (!($this->request instanceof Request) || !($this->response instanceof Response)) {
            return;
        }

        try {
            $this->createRequestLogEntry();
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());
        }
    }

    /**
     * Store request log to database.
     *
     * @throws Throwable
     */
    private function createRequestLogEntry(): void
    {
        /**
         * We want to clear possible existing managements entities before we
         * flush this new `LogRequest` entity to database. This is to prevent
         * not wanted entity state changes to be flushed.
         */
        $this->logRequestResource->getRepository()->getEntityManager()->clear();

        $user = null;
        $apiKey = null;

        if ($this->userId !== null) {
            $user = $this->userResource->getReference($this->userId);
        }

        if ($this->apiKeyId !== null) {
            $apiKey = $this->apiKeyResource->getReference($this->apiKeyId);
        }

        // Create new request log entity
        $entity = new LogRequest(
            $this->sensitiveProperties,
            $this->request,
            $this->response,
            $user,
            $apiKey,
            $this->mainRequest
        );

        $this->logRequestResource->save($entity, true, true);
    }
}
