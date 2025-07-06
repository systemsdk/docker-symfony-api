<?php

declare(strict_types=1);

namespace App\General\Infrastructure\Repository\Traits;

use App\General\Domain\Rest\UuidHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use UnexpectedValueException;

use function preg_replace;

/**
 * @package App\General
 */
trait RepositoryWrappersTrait
{
    /**
     * {@inheritdoc}
     */
    public function getReference(string $id, ?string $entityManagerName = null): ?object
    {
        try {
            $referenceId = UuidHelper::fromString($id);
        } catch (InvalidUuidStringException) {
            $referenceId = $id;
        }

        return $this->getEntityManager($entityManagerName)->getReference($this->getEntityName(), $referenceId);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return array<string, AssociationMapping>
     */
    public function getAssociations(?string $entityManagerName = null): array
    {
        return $this->getClassMetaData($entityManagerName)->getAssociationMappings();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetaData(?string $entityManagerName = null): ClassMetadata
    {
        return $this->getEntityManager($entityManagerName)->getClassMetadata($this->getEntityName());
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager(?string $entityManagerName = null): EntityManager
    {
        $manager = $entityManagerName
            ? $this->managerRegistry->getManager($entityManagerName)
            : $this->managerRegistry->getManagerForClass($this->getEntityName());

        if (!($manager instanceof EntityManager)) {
            throw new UnexpectedValueException(
                'Cannot get entity manager for entity \'' . $this->getEntityName() . '\''
            );
        }

        if ($manager->isOpen() === false) {
            $this->managerRegistry->resetManager($entityManagerName);
            $manager = $this->getEntityManager($entityManagerName);
        }

        return $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(
        ?string $alias = null,
        ?string $indexBy = null,
        ?string $entityManagerName = null
    ): QueryBuilder {
        $alias ??= 'entity';
        $alias = (string)preg_replace('#[\W]#', '', $alias);
        $indexBy = $indexBy !== null ? (string)preg_replace('#[\W]#', '', $indexBy) : null;

        // Create new query builder
        return $this
            ->getEntityManager($entityManagerName)
            ->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntityName(), $alias, $indexBy);
    }
}
