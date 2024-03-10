<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

use function explode;
use function floor;
use function str_replace;

/**
 * @package App\Tool
 */
class LocalizationService implements LocalizationServiceInterface
{
    public function __construct(
        private readonly CacheInterface $appCache,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        return Language::getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales(): array
    {
        return Locale::getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezones(): array
    {
        $output = [];

        try {
            /** @var array<int, array{timezone: string, identifier: string, offset: string, value: string}> $output */
            $output = $this->appCache->get('application_timezone', $this->getClosure());
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedTimezones(): array
    {
        $output = [];

        /** @var array<int, non-empty-string> $identifiers */
        $identifiers = DateTimeZone::listIdentifiers();

        foreach ($identifiers as $identifier) {
            $dateTimeZone = new DateTimeZone($identifier);

            $dateTime = new DateTimeImmutable(timezone: $dateTimeZone);

            $hours = floor($dateTimeZone->getOffset($dateTime) / 3600);
            $minutes = floor(($dateTimeZone->getOffset($dateTime) - ($hours * 3600)) / 60);

            $hours = 'GMT' . ($hours < 0 ? $hours : '+' . $hours);
            $minutes = ($minutes > 0 ? $minutes : '0' . $minutes);

            $output[] = [
                'timezone' => explode('/', $identifier)[0],
                'identifier' => $identifier,
                'offset' => $hours . ':' . $minutes,
                'value' => str_replace('_', ' ', $identifier),
            ];
        }

        return $output;
    }

    private function getClosure(): Closure
    {
        return function (ItemInterface $item): array {
            // One year
            $item->expiresAfter(31536000);

            return $this->getFormattedTimezones();
        };
    }
}
