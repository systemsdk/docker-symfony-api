<?php

declare(strict_types=1);

namespace App\General\Transport\EventSubscriber;

use App\Tool\Application\Service\Interfaces\VersionServiceInterface;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @package App\General
 */
class ResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly VersionServiceInterface $version,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => [
                'onKernelResponse',
                10,
            ],
        ];
    }

    /**
     * Subscriber method to attach API version to every response.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Attach new header
        $event->getResponse()->headers->add([
            'X-API-VERSION' => $this->version->get(),
        ]);
    }
}
