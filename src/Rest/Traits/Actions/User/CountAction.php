<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/User/CountAction.php
 */

namespace App\Rest\Traits\Actions\User;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\CountMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait CountAction
 *
 * Trait to add 'countAction' for REST controllers for 'ROLE_USER' users.
 *
 * @see \App\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\User
 */
trait CountAction
{
    // Traits
    use CountMethod;

    /**
     * Count entities, accessible only for 'ROLE_USER' users.
     *
     * @Route(
     *      path="/count",
     *      methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_USER')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
     *          type="object",
     *          example={"count": "1"},
     *          @SWG\Property(property="count", type="integer"),
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
    public function countAction(Request $request): Response
    {
        return $this->countMethod($request);
    }
}
