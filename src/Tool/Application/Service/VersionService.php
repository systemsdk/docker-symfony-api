<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\General\Domain\Utils\JSON;
use App\Tool\Application\Service\Interfaces\VersionServiceInterface;
use Closure;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

use function array_key_exists;
use function assert;
use function is_array;
use function is_string;

/**
 * @package App\Tool
 */
class VersionService implements VersionServiceInterface
{
    public function __construct(
        private readonly string $projectDir,
        private readonly CacheInterface $appCache,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        $output = '0.0.0';

        try {
            $output = (string)$this->appCache->get('application_version', $this->getClosure());
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return $output;
    }

    private function getClosure(): Closure
    {
        return function (ItemInterface $item): string {
            // One year
            $item->expiresAfter(31536000);
            $composerData = JSON::decode((string)file_get_contents($this->projectDir . '/composer.json'), true);
            assert(is_array($composerData));

            return array_key_exists('version', $composerData) && is_string($composerData['version'])
                ? $composerData['version']
                : '0.0.0';
        };
    }
}
