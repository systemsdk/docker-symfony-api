<?php
declare(strict_types = 1);
/**
 * /src/Resource/UserGroupResource.php
 */

namespace App\Resource;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use App\Entity\UserGroup as Entity;
use App\Repository\UserGroupRepository as Repository;
use App\Rest\RestResource;

/**
 * Class UserGroupResource
 *
 * @package App\Resource
 *
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id): Entity
 * @method Repository getRepository(): Repository
 * @method array<int, Entity> find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null): array
 * @method Entity|null findOne(string $id, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity patch(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity delete(string $id, ?bool $flush = null): EntityInterface
 * @method Entity save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 *
 * @codingStandardsIgnoreEnd
 */
class UserGroupResource extends RestResource
{
    /**
     * Constructor
     */
    public function __construct(Repository $repository)
    {
        $this->setRepository($repository);
    }
}
