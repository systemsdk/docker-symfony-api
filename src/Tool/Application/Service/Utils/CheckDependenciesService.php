<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils;

use App\Tool\Application\Service\Utils\Interfaces\CheckDependenciesServiceInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Traversable;

use function array_map;
use function iterator_to_array;
use function sort;

use const DIRECTORY_SEPARATOR;

/**
 * @package App\Tool
 */
class CheckDependenciesService implements CheckDependenciesServiceInterface
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespaceDirectories(): array
    {
        // Find all main namespace directories under 'tools' directory
        $finder = new Finder()
            ->depth(1)
            ->ignoreDotFiles(true)
            ->directories()
            ->in($this->projectDir . DIRECTORY_SEPARATOR . 'tools/');
        $closure = static fn (SplFileInfo $fileInfo): string => $fileInfo->getPath();
        /** @var Traversable<SplFileInfo> $iterator */
        $iterator = $finder->getIterator();
        // Determine namespace directories
        $directories = array_map($closure, iterator_to_array($iterator));
        sort($directories);

        return $directories;
    }
}
