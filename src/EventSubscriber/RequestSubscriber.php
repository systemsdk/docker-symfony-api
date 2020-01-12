<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/RequestSubscriber.php
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\RequestLoggerService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User as ApplicationUser;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Security\ApiKeyUser;
use Exception;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RequestSubscriber
 *
 * @package App\EventSubscriber
 */
class RequestSubscriber implements EventSubscriberInterface
{
    private RequestLoggerService $requestLogger;
    private TokenStorageInterface $tokenStorage;

    /**
     * Constructor
     *
     * @param RequestLoggerService  $requestLoggerService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RequestLoggerService $requestLoggerService, TokenStorageInterface $tokenStorage)
    {
        // Store logger service
        $this->requestLogger = $requestLoggerService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array<string, array<int, string|int>> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                'onKernelResponse',
                15,
            ],
        ];
    }

    /**
     * Subscriber method to log every request / response.
     *
     * @param ResponseEvent $event
     *
     * @throws Exception
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // We don't want to log /health , /version and OPTIONS requests
        if ($path === '/health'
            || $path === '/version'
            || $request->getRealMethod() === 'OPTIONS'
        ) {
            return;
        }

        $this->process($event);
    }

    /**
     * Method to process current request event.
     *
     * @param ResponseEvent $event
     *
     * @throws Exception
     */
    private function process(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        // Set needed data to logger and handle actual log
        $this->requestLogger->setRequest($request);
        $this->requestLogger->setResponse($event->getResponse());
        /** @var ApplicationUser|ApiKeyUser|null $user */
        $user = $this->getUser();

        if ($user instanceof ApplicationUser) {
            $this->requestLogger->setUser($user);
        } elseif ($user instanceof ApiKeyUser) {
            $this->requestLogger->setApiKey($user->getApiKey());
        }

        $this->requestLogger->setMasterRequest($event->isMasterRequest());
        $this->requestLogger->handle();
    }

    /**
     * Method to get current user from token storage.
     *
     * @return string|mixed|UserInterface|ApplicationUser|ApiKeyUser|null
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        return $token === null || $token instanceof AnonymousToken ? null : $token->getUser();
    }
}
