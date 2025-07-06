<?php

declare(strict_types=1);

namespace App\Tool\Infrastructure\Repository;

use App\Tool\Domain\Repository\Interfaces\ScheduledCommandRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand as Entity;

/**
 * @package App\Tool
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Entity[] findAll()
 *
 * @codingStandardsIgnoreEnd
 */
class ScheduledCommandRepository extends ServiceEntityRepository implements ScheduledCommandRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }

    public function findByCommand(string $command): ?Entity
    {
        return $this->findOneBy([
            'command' => $command,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Entity $entity, ?bool $flush = null): Entity
    {
        $flush ??= true;
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }
}
