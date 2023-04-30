<?php

declare(strict_types=1);

namespace App\Tool\Transport\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use function in_array;

/**
 * Class AcceptLanguageSubscriber
 *
 * @package App\Tool
 */
class AcceptLanguageSubscriber implements EventSubscriberInterface
{
    // Supported locales
    final public const LOCALE_EN = 'en';
    final public const LOCALE_RU = 'ru';
    final public const LOCALE_UA = 'ua';
    final public const LOCALE_FI = 'fi';

    final public const SUPPORTED_LOCALES = [
        self::LOCALE_EN,
        self::LOCALE_RU,
        self::LOCALE_UA,
        self::LOCALE_FI,
    ];

    public function __construct(
        private readonly string $locale,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                'onKernelRequest',
                // Note that this needs to at least `100` to get translation messages as expected
                100,
            ],
        ];
    }

    /**
     * Method to change used locale according to current request.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $request->headers->get('Accept-Language', $this->locale);

        // Ensure that given locale is supported, if not fallback to default.
        if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $this->locale;
        }

        $request->setLocale($locale);
    }
}
