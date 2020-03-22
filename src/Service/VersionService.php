<?php
declare(strict_types = 1);
/**
 * /src/Service/Version.php
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use App\Utils\JSON;
use Exception;
use stdClass;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class VersionService
 *
 * @package App\Service
 */
class VersionService
{
    private string $projectDir;
    private CacheInterface $cache;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param string          $projectDir
     * @param CacheInterface  $appCache
     * @param LoggerInterface $logger
     */
    public function __construct(string $projectDir, CacheInterface $appCache, LoggerInterface $logger)
    {
        $this->projectDir = $projectDir;
        $this->cache = $appCache;
        $this->logger = $logger;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Method to get application version from cache or create new entry to cache with version value from
     * composer.json file.
     *
     * @return string
     */
    public function get(): string
    {
        $output = '0.0.0';

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $output = $this->cache->get('application_version', function (ItemInterface $item): string {
                $item->expiresAfter(31536000); // One year
                /** @var stdClass $composerData */
                $composerData = JSON::decode((string)file_get_contents($this->projectDir . '/composer.json'));

                return (string)($composerData->version ?? '0.0.0');
            });
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return $output;
    }
}
