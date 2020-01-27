<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Admin/FindAction.php
 */

namespace App\Rest\Traits\Actions\Admin;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\FindMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait FindAction
 *
 * Trait to add 'findAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Admin
 */
trait FindAction
{
    // Traits
    use FindMethod;

    /**
     * Get list of entities, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *     path="",
     *     methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(type="string"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Request $request
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}