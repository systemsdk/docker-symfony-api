<?php
declare(strict_types = 1);
/**
 * /src/AutoMapper/User/RequestMapper.php
 */

namespace App\AutoMapper\User;

use App\AutoMapper\RestRequestMapper;
use App\Resource\UserGroupResource;
use App\Entity\UserGroup;

/**
 * Class RequestMapper
 *
 * @package App\AutoMapper
 */
class RequestMapper extends RestRequestMapper
{
    /**
     * @var array<int, string>
     */
    protected static array $properties = [
        'username',
        'firstName',
        'lastName',
        'email',
        'userGroups',
        'password',
    ];

    private UserGroupResource $userGroupResource;


    /**
     * Constructor
     *
     * @param UserGroupResource $userGroupResource
     */
    public function __construct(UserGroupResource $userGroupResource)
    {
        $this->userGroupResource = $userGroupResource;
    }

    /**
     * @param array|array<int, string> $userGroups
     *
     * @return array|UserGroup[]
     */
    protected function transformUserGroups(array $userGroups): array
    {
        return array_map(
            fn (string $userGroupUuid): UserGroup => $this->userGroupResource->getReference($userGroupUuid),
            $userGroups
        );
    }
}
