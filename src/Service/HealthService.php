<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Health;
use App\Repository\HealthRepository;
use Throwable;

/**
 * Class HealthService
 *
 * @package App\Service
 */
final class HealthService
{
    public function __construct(
        private HealthRepository $repository,
    ) {
    }

    /**
     * Method to check that "all" is ok within our application. This will try to do following:
     *  1) Remove data from database
     *  2) Create data to database
     *  3) Read data from database
     *
     * These steps should make sure that at least application database is working as expected.
     *
     * @throws Throwable
     */
    public function check(): ?Health
    {
        $this->repository->cleanup();
        $this->repository->create();

        return $this->repository->read();
    }
}
