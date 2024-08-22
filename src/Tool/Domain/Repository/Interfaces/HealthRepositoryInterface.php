<?php

declare(strict_types=1);

namespace App\Tool\Domain\Repository\Interfaces;

use App\Tool\Domain\Entity\Health as Entity;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Throwable;

/**
 * @package App\Tool
 */
interface HealthRepositoryInterface
{
    /**
     * Method to read value from database
     *
     * @throws NonUniqueResultException
     */
    public function read(): ?Entity;

    /**
     * Method to write new value to database.
     *
     * @throws Throwable
     */
    public function create(): Entity;

    /**
     * Method to cleanup 'health' table.
     *
     * @throws Exception
     */
    public function cleanup(): int;
}
