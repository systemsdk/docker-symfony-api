<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Logged/FindOneAction.php
 */

namespace App\Rest\Traits\Actions\Logged;

use App\Rest\Traits\Methods\FindOneMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Logged
 */
trait FindOneAction
{
    // Traits
    use FindOneMethod;

    /**
     * Find entity, accessible only for 'ROLE_LOGGED' users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_LOGGED')")
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
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
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
