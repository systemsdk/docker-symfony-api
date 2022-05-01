<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\General\Domain\Doctrine\DBAL\Types\EnumLanguageType;
use App\General\Domain\Doctrine\DBAL\Types\EnumLocaleType;
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
 * Class LocalizationService
 *
 * @package App\Tool
 */
class LocalizationService implements LocalizationServiceInterface
{
    public function __construct(
        private CacheInterface $appCache,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        return EnumLanguageType::getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales(): array
    {
        return EnumLocaleType::getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezones(): array
    {
        $output = [];

        try {
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
