<?php
declare(strict_types = 1);
/**
 * /src/Form/DataTransformer/UserGroupTransformer.php
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use App\Resource\UserGroupResource;
use App\Entity\UserGroup;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class UserGroupTransformer
 *
 * @package App\Form\Console\DataTransformer
 */
class UserGroupTransformer implements DataTransformerInterface
{
    private UserGroupResource $resource;

    /**
     * Constructor
     *
     * @param UserGroupResource $resource
     */
    public function __construct(UserGroupResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Transforms an object (Role) to a string (Role id).
     *
     * @param array|UserGroup[]|mixed|null $userGroups
     *
     * @return array
     */
    public function transform($userGroups): ?array
    {
        $output = [];

        if (is_array($userGroups)) {
            $iterator =
                /**
                 * @param string|UserGroup $userGroup
                 *
                 * @return string
                 */
                fn ($userGroup): string => $userGroup instanceof UserGroup ? $userGroup->getId() : $userGroup;

            $output = array_values(array_map('\strval', array_map($iterator, $userGroups)));
        }

        return $output;
    }

    /**
     * Transforms a string (Role id) to an object (Role).
     *
     * @param array|mixed $userGroups
     *
     * @throws TransformationFailedException if object (issue) is not found.
     *
     * @return array|UserGroup[]|null
     */
    public function reverseTransform($userGroups): ?array
    {
        $output = null;

        if (is_array($userGroups)) {
            $iterator = function (string $groupId): UserGroup {
                /** @var UserGroup|null $group */
                $group = $this->resource->findOne($groupId);

                if ($group === null) {
                    throw new TransformationFailedException(sprintf(
                        'User group with id "%s" does not exist!',
                        $groupId
                    ));
                }

                return $group;
            };

            $output = array_values(array_map($iterator, $userGroups));
        }

        return $output;
    }
}
