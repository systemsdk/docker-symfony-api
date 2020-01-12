<?php
declare(strict_types = 1);
/**
 * /src/Form/DataTransformer/RoleTransformer.php
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use App\Resource\RoleResource;
use App\Entity\Role;
use Throwable;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RoleTransformer
 *
 * @package App\Form\Console\DataTransformer
 */
class RoleTransformer implements DataTransformerInterface
{
    private RoleResource $resource;

    /**
     * Constructor
     *
     * @param RoleResource $resource
     */
    public function __construct(RoleResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Transforms an object (Role) to a string (Role id).
     *
     * @param Role|mixed|null $role
     *
     * @return string
     */
    public function transform($role): string
    {
        return $role instanceof Role ? $role->getId() : '';
    }

    /**
     * Transforms a string (Role id) to an object (Role).
     *
     * @param string|mixed|null $roleName
     *
     * @throws TransformationFailedException if object (issue) is not found.
     * @throws Throwable
     *
     * @return Role|null
     */
    public function reverseTransform($roleName): ?Role
    {
        $role = null;

        if ($roleName !== null) {
            $role = $this->resource->findOne((string)$roleName, false);

            if ($role === null) {
                throw new TransformationFailedException(sprintf(
                    'Role with name "%s" does not exist!',
                    (string)$roleName
                ));
            }
        }

        return $role;
    }
}
