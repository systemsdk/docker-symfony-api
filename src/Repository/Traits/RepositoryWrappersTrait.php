<?php

declare(strict_types=1);

namespace App\Repository\Traits;

use App\Rest\UuidHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use UnexpectedValueException;

/**
 * Class RepositoryWrappersTrait
 *
 * @package App\Repository\Traits
 */
trait RepositoryWrappersTrait
{
    /**
     * {@inheritdoc}
     */
    public function getReference(string $id): ?object
    {
        try {
            $referenceId = UuidHelper::fromString($id);
        } catch (InvalidUuidStringException) {
            $referenceId = $id;
        }

        return $this->getEntityManager()->getReference($this->getEntityName(), $referenceId);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return array<string, array<string, mixed>>
     */
    public function getAssociations(): array
    {
        return $this->getClassMetaData()->getAssociationMappings();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetaData(): ClassMetadataInfo
    {
        return $this->getEntityManager()->getClassMetadata($this->getEntityName());
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager(): EntityManager
    {
        $manager = $this->managerRegistry->getManagerForClass($this->getEntityName());

        if (!($manager instanceof EntityManager)) {
            throw new UnexpectedValueException(
                'Cannot get entity manager for entity \'' . $this->getEntityName() . '\''
            );
        }

        if ($manager->isOpen() === false) {
            $this->managerRegistry->resetManager();
            $manager = $this->getEntityManager();
        }

        return $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(?string $alias = null, ?string $indexBy = null): QueryBuilder
    {
        $alias ??= 'entity';

        // Create new query builder
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntityName(), $alias, $indexBy);
    }
}
