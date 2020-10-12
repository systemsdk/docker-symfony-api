<?php
declare(strict_types = 1);
/**
 * /src/AutoMapper/User/RequestMapper.php
 */

namespace App\AutoMapper\User;

use App\AutoMapper\RestRequestMapper;
use App\Entity\UserGroup;
use App\Resource\UserGroupResource;

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
        'language',
        'locale',
        'timezone',
        'userGroups',
        'password',
    ];

    private UserGroupResource $userGroupResource;

    /**
     * Constructor
     */
    public function __construct(UserGroupResource $userGroupResource)
    {
        $this->userGroupResource = $userGroupResource;
    }

    /**
     * @param array<int, string> $userGroups
     *
     * @return array<int, UserGroup>
     */
    protected function transformUserGroups(array $userGroups): array
    {
        return array_map(
            fn (string $userGroupUuid): UserGroup => $this->userGroupResource->getReference($userGroupUuid),
            $userGroups
        );
    }
}
