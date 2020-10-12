<?php
declare(strict_types = 1);
/**
 * /src/Service/RequestLoggerService.php
 */

namespace App\Service;

use App\Entity\ApiKey;
use App\Entity\LogRequest;
use App\Entity\User;
use App\Resource\LogRequestResource;
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
    private LogRequestResource $resource;
    private LoggerInterface $logger;
    private array $sensitiveProperties;
    private ?Response $response = null;
    private ?Request $request = null;
    private ?User $user = null;
    private ?ApiKey $apiKey = null;
    private bool $masterRequest = false;

    /**
     * Constructor
     */
    public function __construct(LogRequestResource $resource, LoggerInterface $logger, array $sensitiveProperties)
    {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->sensitiveProperties = $sensitiveProperties;
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
    public function setUser(?User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey(?ApiKey $apiKey = null): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMasterRequest(bool $masterRequest): self
    {
        $this->masterRequest = $masterRequest;

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
     * Store request log.
     *
     * @throws Throwable
     */
    private function createRequestLogEntry(): void
    {
        // Create new request log entity
        $entity = new LogRequest(
            $this->sensitiveProperties,
            $this->request,
            $this->response,
            $this->user,
            $this->apiKey,
            $this->masterRequest
        );
        $this->resource->save($entity, true, true);
    }
}
