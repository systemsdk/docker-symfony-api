<?php
declare(strict_types = 1);
/**
 * /src/Repository/Traits/RepositoryWrappers.php
 */

namespace App\Repository\Traits;

use App\Rest\UuidHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Throwable;
use UnexpectedValueException;

/**
 * Class RepositoryWrappers
 *
 * @package App\Repository\Traits
 *
 * @method string getEntityName(): string
 */
trait RepositoryWrappers
{
    protected ManagerRegistry $managerRegistry;

    /**
     * {@inheritdoc}
     */
    public function getReference(string $id)
    {
        try {
            $referenceId = UuidHelper::fromString($id);
        } catch (InvalidUuidStringException $exception) {
            (static fn (Throwable $exception): string => (string)$exception)($exception);

            $referenceId = $id;
        }

        return $this->getEntityManager()->getReference($this->getEntityName(), $referenceId);
    }

    /**
     * {@inheritdoc}
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
