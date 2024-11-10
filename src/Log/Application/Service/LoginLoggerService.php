<?php

declare(strict_types=1);

namespace App\Log\Application\Service;

use App\Log\Application\Resource\LogLoginResource;
use App\Log\Application\Service\Interfaces\LoginLoggerServiceInterface;
use App\Log\Domain\Entity\LogLogin;
use App\Log\Domain\Enum\LogLogin as LogLoginEnum;
use App\User\Domain\Entity\User;
use BadMethodCallException;
use DeviceDetector\DeviceDetector;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * @package App\Log
 */
class LoginLoggerService implements LoginLoggerServiceInterface
{
    private readonly DeviceDetector $deviceDetector;
    private ?User $user = null;

    public function __construct(
        private readonly LogLoginResource $logLoginResource,
        private readonly RequestStack $requestStack,
    ) {
        $this->deviceDetector = new DeviceDetector();
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function setUser(?User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function process(LogLoginEnum $type): void
    {
        // Get current request
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new BadMethodCallException('Could not get request from current request stack');
        }

        // Parse user agent data with device detector
        $this->deviceDetector->setUserAgent($request->headers->get('User-Agent', ''));
        $this->deviceDetector->parse();
        // Create entry
        $this->createEntry($type, $request);
    }

    /**
     * Method to create new login entry and store it to database.
     *
     * @throws Throwable
     */
    private function createEntry(LogLoginEnum $type, Request $request): void
    {
        $entry = new LogLogin($type, $request, $this->deviceDetector, $this->user);
        // And store entry to database
        $this->logLoginResource->save($entry, true);
    }
}
