<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\v1\User;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Methods;
use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Class DeleteUserController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\User
 */
#[AsController]
class DeleteUserController extends Controller
{
    use Methods\DeleteMethod;

    public function __construct(
        UserResource $resource,
    ) {
        parent::__construct($resource);
    }

    /**
     * Delete user entity, accessible only for 'ROLE_ROOT' users.
     *
     * @OA\Response(
     *     response=200,
     *     description="deleted",
     *     @OA\JsonContent(
     *         ref=@Model(
     *             type=User::class,
     *             groups={"User"},
     *         ),
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
     *  )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/v1/user/{user}',
        requirements: [
            'user' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_DELETE],
    )]
    #[IsGranted(Role::ROOT->value)]
    public function __invoke(Request $request, User $user, User $loggedInUser): Response
    {
        if ($loggedInUser === $user) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'You cannot remove yourself...');
        }

        return $this->deleteMethod($request, $user->getId());
    }
}
