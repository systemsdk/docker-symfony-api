<?php
declare(strict_types = 1);
/**
 * /src/Service/RequestLoggerService.php
 */

namespace App\Service;

use App\Service\Interfaces\RequestLoggerServiceInterface;
use App\Entity\ApiKey;
use App\Entity\LogRequest;
use App\Entity\User;
use App\Utils\Traits\LoggerAware;
use App\Resource\LogRequestResource;
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
    // Traits
    use LoggerAware;

    private LogRequestResource $resource;
    private ?Response $response = null;
    private ?Request $request = null;
    private ?User $user = null;
    private ?ApiKey $apiKey = null;
    private bool $masterRequest = false;

    /**
     * Constructor
     *
     * @param LogRequestResource $resource
     */
    public function __construct(LogRequestResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Setter for response object.
     *
     * @param Response $response
     *
     * @return RequestLoggerServiceInterface
     */
    public function setResponse(Response $response): RequestLoggerServiceInterface
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Setter for request object.
     *
     * @param Request $request
     *
     * @return RequestLoggerServiceInterface
     */
    public function setRequest(Request $request): RequestLoggerServiceInterface
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Setter method for current user.
     *
     * @param User|null $user
     *
     * @return RequestLoggerServiceInterface
     */
    public function setUser(?User $user = null): RequestLoggerServiceInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Setter method for current api key
     *
     * @param ApiKey|null $apiKey
     *
     * @return RequestLoggerServiceInterface
     */
    public function setApiKey(?ApiKey $apiKey = null): RequestLoggerServiceInterface
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Setter method for 'master request' info.
     *
     * @param bool $masterRequest
     *
     * @return RequestLoggerServiceInterface
     */
    public function setMasterRequest(bool $masterRequest): RequestLoggerServiceInterface
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
        $entity = new LogRequest($this->request, $this->response, $this->user, $this->apiKey, $this->masterRequest);
        $this->resource->save($entity, true, true);
    }
}
