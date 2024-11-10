<?php

declare(strict_types=1);

namespace App\General\Transport\EventSubscriber;

use App\ApiKey\Application\Security\ApiKeyUser;
use App\Log\Application\Service\RequestLoggerService;
use App\User\Application\Security\SecurityUser;
use App\User\Application\Security\UserTypeIdentification;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Throwable;

use function array_filter;
use function array_values;
use function in_array;
use function str_contains;
use function substr;

/**
 * @package App\General
 *
 * @property array<int, string> $ignoredRoutes
 */
class RequestLogSubscriber implements EventSubscriberInterface
{
    /**
     * @param array<int, string> $ignoredRoutes
     */
    public function __construct(
        private readonly RequestLoggerService $requestLoggerService,
        private readonly UserTypeIdentification $userService,
        private readonly array $ignoredRoutes,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => [
                'onTerminateEvent',
                15,
            ],
        ];
    }

    /**
     * Subscriber method to log every request / response.
     *
     * @throws Throwable
     */
    public function onTerminateEvent(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        $filter = static fn (string $route): bool =>
            str_contains($route, '/*') && str_contains($path, substr($route, 0, -2));

        // We don't want to log OPTIONS requests, /_profiler* -path, ignored routes and wildcard ignored routes
        if (
            $request->getRealMethod() === Request::METHOD_OPTIONS
            || str_contains($path, '/_profiler')
            || in_array($path, $this->ignoredRoutes, true)
            || array_values(array_filter($this->ignoredRoutes, $filter)) !== []
        ) {
            return;
        }

        $this->process($event);
    }

    /**
     * Method to process current request event.
     *
     * @throws Throwable
     */
    private function process(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        // Set needed data to logger and handle actual log
        $this->requestLoggerService->setRequest($request);
        $this->requestLoggerService->setResponse($event->getResponse());
        $identify = $this->userService->getIdentity();

        if ($identify instanceof SecurityUser) {
            $this->requestLoggerService->setUserId($identify->getUserIdentifier());
        } elseif ($identify instanceof ApiKeyUser) {
            $this->requestLoggerService->setApiKeyId($identify->getApiKeyIdentifier());
        }

        $this->requestLoggerService->setMainRequest($event->isMainRequest());
        $this->requestLoggerService->handle();
    }
}
