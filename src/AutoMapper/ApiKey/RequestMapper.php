<?php
declare(strict_types = 1);
/**
 * /src/AutoMapper/ApiKey/RequestMapper.php
 */

namespace App\AutoMapper\ApiKey;

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
     * Properties to map to destination object.
     *
     * @var array<int, string>
     */
    protected static array $properties = [
        'description',
        'userGroups',
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
