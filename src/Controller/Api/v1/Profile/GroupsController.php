<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Profile;

use App\Entity\User;
use App\Entity\UserGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class GroupsController
 *
 * @OA\Tag(name="Profile")
 *
 * @package App\Controller\Api\v1\Profile
 */
class GroupsController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * Get current user user groups, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="User groups",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=UserGroup::class,
     *                 groups={"set.UserProfileGroups"},
     *             ),
     *         ),
     *      ),
     *  )
     * @OA\Response(
     *     response=401,
     *     description="Invalid token (not found or expired)",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 401, "message": "JWT Token not found"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"code": 403, "message": "Access denied"},
     *        @OA\Property(property="code", type="integer", description="Error code"),
     *        @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     */
    #[Route(
        path: '/v1/profile/groups',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize(
                $loggedInUser->getUserGroups()->toArray(),
                'json',
                [
                    'groups' => UserGroup::SET_USER_PROFILE_GROUPS,
                ],
            ),
            json: true,
        );
    }
}
