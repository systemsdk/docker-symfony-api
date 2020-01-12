<?php
declare(strict_types = 1);
/**
 * /src/Rest/Interfaces/RestResourceInterfaces.php
 */

namespace App\Rest\Interfaces;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use App\Repository\Interfaces\BaseRepositoryInterface;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use UnexpectedValueException;

/**
 * Interface ResourceInterface
 *
 * @package App\Rest\Interfaces
 */
interface RestResourceInterface
{
    /**
     * Getter method for entity repository.
     *
     * @return BaseRepositoryInterface
     */
    public function getRepository(): BaseRepositoryInterface;

    /**
     * Setter method for repository.
     *
     * @param BaseRepositoryInterface $repository
     *
     * @return RestResourceInterface
     */
    public function setRepository(BaseRepositoryInterface $repository): self;

    /**
     * Getter for used validator.
     *
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface;

    /**
     * Setter for used validator.
     *
     * @param ValidatorInterface $validator
     *
     * @return RestResourceInterface
     */
    public function setValidator(ValidatorInterface $validator): self;

    /**
     * Getter method for used DTO class for this REST service.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getDtoClass(): string;

    /**
     * Setter for used DTO class.
     *
     * @param string $dtoClass
     *
     * @return RestResourceInterface
     */
    public function setDtoClass(string $dtoClass): self;

    /**
     * Getter method for current entity name.
     *
     * @return string
     */
    public function getEntityName(): string;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @param string $id The entity identifier.
     *
     * @throws ORMException
     *
     * @return Proxy|object|null
     */
    public function getReference(string $id);

    /**
     * Getter method for all associations that current entity contains.
     *
     * @return array
     */
    public function getAssociations(): array;

    /**
     * Getter method DTO class with loaded entity data.
     *
     * @param string           $id
     * @param string           $dtoClass
     * @param RestDtoInterface $dto
     * @param bool|null        $patch
     *
     * @throws Throwable
     *
     * @return RestDtoInterface
     */
    public function getDtoForEntity(
        string $id,
        string $dtoClass,
        RestDtoInterface $dto,
        ?bool $patch = null
    ): RestDtoInterface;

    /**
     * Generic find method to return an array of items from database. Return value is an array of specified repository
     * entities.
     *
     * @param array|null $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @param array|null $search
     *
     * @throws Throwable
     *
     * @return EntityInterface[]
     */
    public function find(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null
    ): array;

    /**
     * Generic findOne method to return single item from database. Return value is single entity from specified
     * repository.
     *
     * @param string    $id
     * @param bool|null $throwExceptionIfNotFound
     *
     * @throws Throwable
     *
     * @return EntityInterface|null
     */
    public function findOne(string $id, ?bool $throwExceptionIfNotFound = null): ?EntityInterface;

    /**
     * Generic findOneBy method to return single item from database by given criteria. Return value is single entity
     * from specified repository or null if entity was not found.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param bool|null  $throwExceptionIfNotFound
     *
     * @throws Throwable
     *
     * @return EntityInterface|null
     */
    public function findOneBy(
        array $criteria,
        ?array $orderBy = null,
        ?bool $throwExceptionIfNotFound = null
    ): ?EntityInterface;

    /**
     * Generic count method to return entity count for specified criteria and search terms.
     *
     * @param array|null $criteria
     * @param array|null $search
     *
     * @throws Throwable
     *
     * @return int
     */
    public function count(?array $criteria = null, ?array $search = null): int;

    /**
     * Generic method to create new item (entity) to specified database repository. Return value is created entity for
     * specified repository.
     *
     * @param RestDtoInterface $dto
     * @param bool|null        $flush
     * @param bool|null        $skipValidation
     *
     * @throws Throwable
     *
     * @return EntityInterface
     */
    public function create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface;

    /**
     * Generic method to update specified entity with new data.
     *
     * @param string           $id
     * @param RestDtoInterface $dto
     * @param bool|null        $flush
     * @param bool|null        $skipValidation
     *
     * @throws Throwable
     *
     * @return EntityInterface
     */
    public function update(
        string $id,
        RestDtoInterface $dto,
        ?bool $flush = null,
        ?bool $skipValidation = null
    ): EntityInterface;

    /**
     * Generic method to patch specified entity with new data.
     *
     * @param string           $id
     * @param RestDtoInterface $dto
     * @param bool|null        $flush
     * @param bool|null        $skipValidation
     *
     * @throws Throwable
     *
     * @return EntityInterface
     */
    public function patch(
        string $id,
        RestDtoInterface $dto,
        ?bool $flush = null,
        ?bool $skipValidation = null
    ): EntityInterface;

    /**
     * Generic method to delete specified entity from database.
     *
     * @param string    $id
     * @param bool|null $flush
     *
     * @throws Throwable
     *
     * @return EntityInterface
     */
    public function delete(string $id, ?bool $flush = null): EntityInterface;

    /**
     * Generic ids method to return an array of id values from database. Return value is an array of specified
     * repository entity id values.
     *
     * @param array|null $criteria
     * @param array|null $search
     *
     * @throws Throwable
     *
     * @return array
     */
    public function getIds(?array $criteria = null, ?array $search = null): array;

    /**
     * Generic method to save given entity to specified repository. Return value is created entity.
     *
     * @param EntityInterface $entity
     * @param bool|null       $flush
     * @param bool|null       $skipValidation
     *
     * @throws Throwable
     *
     * @return EntityInterface
     */
    public function save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface;
}
