<?php

declare(strict_types=1);

namespace App\General\Transport\Command;

use Closure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

use function array_map;

/**
 * @package App\General
 */
class HelperConfigure
{
    /**
     * @param array<int, array<string, int|string>> $parameters
     *
     * @throws InvalidArgumentException
     */
    public static function configure(Command $command, array $parameters): void
    {
        // Configure command
        $command->setDefinition(new InputDefinition(array_map(self::getParameterIterator(), $parameters)));
    }

    /**
     * @throws InvalidArgumentException @phpstan-ignore throws.unusedType
     */
    private static function getParameterIterator(): Closure
    {
        return static fn (array $input): InputOption => new InputOption(
            $input['name'],
            $input['shortcut'] ?? null,
            $input['mode'] ?? InputOption::VALUE_OPTIONAL,
            $input['description'] ?? '',
            $input['default'] ?? null,
            $input['suggestedValues'] ?? [],
        );
    }
}
