<?php
declare(strict_types = 1);
/**
 * /src/Service/Interfaces/RequestLoggerServiceInterface.php
 */

namespace App\Service\Interfaces;

use App\Entity\ApiKey;
use App\Entity\User;
use App\Resource\LogRequestResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface RequestLoggerServiceInterface
 *
 * @package App\Service\Interfaces
 */
interface RequestLoggerServiceInterface
{
    /**
     * Constructor
     *
     * @param LogRequestResource $resource
     * @param LoggerInterface    $logger
     */
    public function __construct(LogRequestResource $resource, LoggerInterface $logger);

    /**
     * Setter for response object.
     *
     * @param Response $response
     *
     * @return RequestLoggerServiceInterface
     */
    public function setResponse(Response $response): self;

    /**
     * Setter for request object.
     *
     * @param Request $request
     *
     * @return RequestLoggerServiceInterface
     */
    public function setRequest(Request $request): self;

    /**
     * Setter method for current user.
     *
     * @param User|null $user
     *
     * @return RequestLoggerServiceInterface
     */
    public function setUser(?User $user = null): self;

    /**
     * Setter method for current api key
     *
     * @param ApiKey|null $apiKey
     *
     * @return RequestLoggerServiceInterface
     */
    public function setApiKey(?ApiKey $apiKey = null): self;

    /**
     * Setter method for 'master request' info.
     *
     * @param bool $masterRequest
     *
     * @return RequestLoggerServiceInterface
     */
    public function setMasterRequest(bool $masterRequest): self;

    /**
     * Method to handle current response and log it to database.
     */
    public function handle(): void;
}
