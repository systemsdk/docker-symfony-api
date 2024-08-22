<?php

declare(strict_types=1);

namespace App\General\Transport\Command\Traits;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @package App\General
 */
trait GetApplicationTrait
{
    /**
     * @throws RuntimeException
     */
    public function getApplication(): Application
    {
        return parent::getApplication()
            ?? throw new RuntimeException('Cannot determine application for console command to use.');
    }
}
