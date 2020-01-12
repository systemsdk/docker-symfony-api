<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/ProfileController.php
 */

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\Role;
use App\Security\RolesService;
use App\Utils\JSON;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use JsonException;

/**
 * Class ProfileController
 *
 * @Route(
 *      path="/profile",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @SWG\Tag(name="Profile")
 *
 * @package App\Controller\Api
 */
class ProfileController
{
    /**
     * Get current user profile data, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="",
     *     methods={"GET"}
     *  );
     *
     * @SWG\Response(
     *      response=200,
     *      description="User profile data",
     *      @SWG\Schema(
     *          ref=@Model(
     *              type=User::class,
     *              groups={"User", "User.userGroups", "User.roles", "UserGroup", "UserGroup.role"},
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *  )
     *
     * @param SerializerInterface   $serializer
     * @param RolesService          $rolesService
     * @param User                  $loggedInUser
     *
     * @throws JsonException
     *
     * @return JsonResponse
     */
    public function profileAction(
        SerializerInterface $serializer,
        RolesService $rolesService,
        User $loggedInUser
    ): JsonResponse {
        // Get serializer groups for current user instance
        $groups = $this->getSerializationGroupsForUser();
        /** @var array<string, string|array> $output */
        $output = JSON::decode($serializer->serialize($loggedInUser, 'json', ['groups' => $groups]), true);
        /** @var array<int, string> $roles */
        $roles = $output['roles'];
        $output['roles'] = $rolesService->getInheritedRoles($roles);

        return new JsonResponse($output);
    }

    /**
     * Get current user roles as an array, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/roles",
     *     methods={"GET"},
     *  );
     *
     * @SWG\Response(
     *      response=200,
     *      description="User roles",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=Role::class,
     *                  groups={"Role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *  )
     *
     * @param RolesService $rolesService
     * @param User         $loggedInUser
     *
     * @return JsonResponse
     */
    public function rolesAction(RolesService $rolesService, User $loggedInUser): JsonResponse
    {
        return new JsonResponse($rolesService->getInheritedRoles($loggedInUser->getRoles()));
    }

    /**
     * Get current user user groups, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/groups",
     *     methods={"GET"}
     *  );
     *
     * @SWG\Response(
     *      response=200,
     *      description="User groups",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              ref=@Model(
     *                  type=UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Invalid token",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *          "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *      },
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *  )
     *
     * @param SerializerInterface $serializer
     * @param User                $loggedInUser
     *
     * @return JsonResponse
     */
    public function groupsAction(SerializerInterface $serializer, User $loggedInUser): JsonResponse
    {
        $groups = [
            'groups' => $this->getUserGroupGroups(),
        ];

        return new JsonResponse(
            $serializer->serialize($loggedInUser->getUserGroups(), 'json', $groups),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @return array
     */
    private function getSerializationGroupsForUser(): array
    {
        return array_merge(
            [
                'User',
                'User.userGroups',
                'User.roles',
            ],
            $this->getUserGroupGroups()
        );
    }

    /**
     * @return array
     */
    private function getUserGroupGroups(): array
    {
        return [
            'UserGroup',
            'UserGroup.role',
        ];
    }
}
