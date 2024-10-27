<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Scheduler\Interfaces;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * @package App\Tool
 */
interface ScheduledCommandServiceInterface
{
    public function findByCommand(string $command): ?ScheduledCommand;

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(
        string $name,
        string $command,
        string $cronExpression,
        string $logFile,
        int $priority = 100
    ): ScheduledCommand;
}
