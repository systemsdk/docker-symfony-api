<?php

declare(strict_types=1);

namespace App\Tool\Infrastructure\Service\Utils;

use App\Tool\Domain\Service\Utils\Interfaces\CheckDatabaseConnectionServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @package App\Tool
 */
class CheckDatabaseConnectionService implements CheckDatabaseConnectionServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function checkConnection(): bool
    {
        $connection = $this->em->getConnection();
        $statement = $connection->prepare('SHOW TABLES');
        $statement->executeQuery();

        return true;
    }
}
