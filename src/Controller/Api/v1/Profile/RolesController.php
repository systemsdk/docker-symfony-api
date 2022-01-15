<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Profile;

use App\Entity\User;
use App\Security\RolesService;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class RolesController
 *
 * @OA\Tag(name="Profile")
 *
 * @package App\Controller\Api\v1\Profile
 */
class RolesController
{
    public function __construct(
        private RolesService $rolesService,
    ) {
    }

    /**
     * Get current user roles as an array, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="User roles",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
     *     ),
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
     *  )
     */
    #[Route(
        path: '/v1/profile/roles',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles($loggedInUser->getRoles()));
    }
}
