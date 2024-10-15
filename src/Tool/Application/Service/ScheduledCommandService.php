<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\Tool\Application\Service\Interfaces\ScheduledCommandServiceInterface;
use App\Tool\Domain\Repository\Interfaces\ScheduledCommandRepositoryInterface;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * @package App\Tool
 */
class ScheduledCommandService implements ScheduledCommandServiceInterface
{
    public function __construct(
        private readonly ScheduledCommandRepositoryInterface $scheduledCommandRepository,
    ) {
    }

    public function findByCommand(string $command): ?ScheduledCommand
    {
        return $this->scheduledCommandRepository->findByCommand($command);
    }

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
    ): ScheduledCommand {
        $scheduledCommand = (new ScheduledCommand())
            ->setName($name)
            ->setCommand($command)
            ->setCronExpression($cronExpression)
            ->setPriority($priority)
            ->setLastExecution(new DateTime())
            ->setLogFile($logFile)
            ->setExecuteImmediately(false)
            ->setDisabled(false);

        return $this->scheduledCommandRepository->save($scheduledCommand);
    }
}
