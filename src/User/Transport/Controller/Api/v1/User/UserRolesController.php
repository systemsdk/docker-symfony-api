<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\v1\User;

use App\Role\Application\Security\RolesService;
use App\User\Domain\Entity\User;
use OpenApi\Annotations as OA;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class UserRolesController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\User
 */
class UserRolesController
{
    public function __construct(
        private readonly RolesService $rolesService,
    ) {
    }

    /**
     * Fetch specified user roles, accessible only for 'IS_USER_HIMSELF' or 'ROLE_ROOT' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="Specified user roles",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
     *     ),
     * )
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
     *         type="object",
     *         example={"code": 403, "message": "Access denied"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     */
    #[Route(
        path: '/v1/user/{user}/roles',
        requirements: [
            'user' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(new Expression('is_granted("IS_USER_HIMSELF", object) or "ROLE_ROOT" in role_names'), 'user')]
    public function __invoke(User $user): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles($user->getRoles()));
    }
}
