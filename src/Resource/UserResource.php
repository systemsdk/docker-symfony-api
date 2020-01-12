<?php
declare(strict_types = 1);
/**
 * /src/Resource/UserResource.php
 */

namespace App\Resource;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use App\Entity\User as Entity;
use App\Entity\UserGroup;
use App\Repository\UserRepository as Repository;
use App\Rest\RestResource;
use App\Security\RolesService;
use Throwable;

/**
 * Class UserResource
 *
 * @package App\Resource
 *
 * @codingStandardsIgnoreStart
 *
 * @method Entity      getReference(string $id): Entity
 * @method Repository  getRepository(): Repository
 * @method Entity[]    find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null): array
 * @method Entity|null findOne(string $id, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity      create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity      update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity      patch(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity      delete(string $id, ?bool $flush = null): EntityInterface
 * @method Entity      save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 *
 * @codingStandardsIgnoreEnd
 */
class UserResource extends RestResource
{
    private RolesService $rolesService;

    /**
     * Class constructor.
     *
     * @param Repository $repository
     * @param RolesService $rolesService
     */
    public function __construct(Repository $repository, RolesService $rolesService)
    {
        $this->setRepository($repository);
        $this->rolesService = $rolesService;
    }

    /**
     * Method to fetch users for specified user group, note that this method will also check user role inheritance so
     * return value will contain all users that belong to specified user group via role inheritance.
     *
     * @param UserGroup $userGroup
     *
     * @throws Throwable
     *
     * @return Entity[]
     */
    public function getUsersForGroup(UserGroup $userGroup): array
    {
        /**
         * Filter method to see if specified user belongs to certain user group.
         *
         * @param Entity $user
         *
         * @return bool
         */
        $filter = function (Entity $user) use ($userGroup): bool {
            $user->setRolesService($this->rolesService);

            return in_array($userGroup->getRole()->getId(), $user->getRoles(), true);
        };

        /** @var Entity[] $users */
        $users = $this->find();

        return array_values(array_filter($users, $filter));
    }
}
