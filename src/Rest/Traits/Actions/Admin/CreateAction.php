<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Admin/CreateAction.php
 */

namespace App\Rest\Traits\Actions\Admin;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\CreateMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Admin
 */
trait CreateAction
{
    // Traits
    use CreateMethod;

    /**
     * Create entity, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *     path="",
     *     methods={"POST"},
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @SWG\Response(
     *      response=201,
     *      description="created",
     * @SWG\Schema(
     *          type="object",
     *          example={},
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     * @SWG\Schema(
     *          type="object",
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
