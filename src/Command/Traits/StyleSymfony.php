<?php
declare(strict_types = 1);
/**
 * /src/Command/Traits/StyleSymfony.php
 */

namespace App\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Trait StyleSymfony
 *
 * @package App\Command\Traits
 */
trait StyleSymfony
{
    /**
     * Method to get SymfonyStyle object for console commands.
     */
    protected function getSymfonyStyle(
        InputInterface $input,
        OutputInterface $output,
        ?bool $clearScreen = null
    ): SymfonyStyle {
        $clearScreen ??= true;
        $io = new SymfonyStyle($input, $output);

        if ($clearScreen) {
            $io->write("\033\143");
        }

        return $io;
    }
}
