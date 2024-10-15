<?php

declare(strict_types=1);

namespace App\Tool\Domain\Repository\Interfaces;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand as Entity;

/**
 * @package App\Tool
 */
interface ScheduledCommandRepositoryInterface
{
    public function findByCommand(string $command): ?Entity;

    /**
     * Method to persist specified entity to database.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Entity $entity, ?bool $flush = null): Entity;
}
