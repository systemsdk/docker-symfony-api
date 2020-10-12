<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Admin/CountAction.php
 */

namespace App\Rest\Traits\Actions\Admin;

use App\Rest\Traits\Methods\CountMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CountAction
 *
 * Trait to add 'countAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Admin
 */
trait CountAction
{
    // Traits
    use CountMethod;

    /**
     * Count entities, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *     path="/count",
     *     methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="object",
     *          example={"count": "1"},
     * @SWG\Property(property="count", type="integer"),
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
    public function countAction(Request $request): Response
    {
        return $this->countMethod($request);
    }
}
