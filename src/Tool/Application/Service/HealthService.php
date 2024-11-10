<?php

declare(strict_types=1);

namespace App\Tool\Application\Service;

use App\Tool\Application\Service\Interfaces\HealthServiceInterface;
use App\Tool\Domain\Entity\Health;
use App\Tool\Domain\Repository\Interfaces\HealthRepositoryInterface;

/**
 * @package App\Tool
 */
readonly class HealthService implements HealthServiceInterface
{
    /**
     * @param \App\Tool\Infrastructure\Repository\HealthRepository $repository
     */
    public function __construct(
        private HealthRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ?Health
    {
        $this->repository->cleanup();
        $this->repository->create();

        return $this->repository->read();
    }
}
