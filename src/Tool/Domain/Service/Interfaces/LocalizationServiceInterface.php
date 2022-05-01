<?php

declare(strict_types=1);

namespace App\Tool\Domain\Service\Interfaces;

use App\General\Domain\Doctrine\DBAL\Types\EnumLanguageType;
use App\General\Domain\Doctrine\DBAL\Types\EnumLocaleType;
use Throwable;

/**
 * Interface LocalizationServiceInterface
 *
 * @package App\Tool
 */
interface LocalizationServiceInterface
{
    public const DEFAULT_LANGUAGE = EnumLanguageType::LANGUAGE_EN;
    public const DEFAULT_LOCALE = EnumLocaleType::LOCALE_EN;
    public const DEFAULT_TIMEZONE = 'Europe/Kiev';

    /**
     * @return array<int, string>
     */
    public function getLanguages(): array;

    /**
     * @return array<int, string>
     */
    public function getLocales(): array;

    /**
     * @return array<int, array{timezone: string, identifier: string,  offset: string, value: string}>
     */
    public function getTimezones(): array;

    /**
     * @return array<int, array{timezone: string, identifier: string,  offset: string, value: string}>
     *
     * @throws Throwable
     */
    public function getFormattedTimezones(): array;
}
