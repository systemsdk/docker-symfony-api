<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/User/CreateAction.php
 */

namespace App\Rest\Traits\Actions\User;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\CreateMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for 'ROLE_USER' users.
 *
 * @see \App\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\User
 */
trait CreateAction
{
    // Traits
    use CreateMethod;

    /**
     * Create entity, accessible only for 'ROLE_USER' users.
     *
     * @Route(
     *      path="",
     *      methods={"POST"},
     *  )
     *
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Response(
     *     response=201,
     *     description="created",
     *     @OA\Schema(
     *         type="object",
     *         example={},
     *     ),
     * )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\Schema(
     *         type="object",
     *         example={
     *             "Access denied": "{code: 403, message: 'Access denied'}",
     *         },
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
