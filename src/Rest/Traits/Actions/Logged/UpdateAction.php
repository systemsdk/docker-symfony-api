<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Logged/UpdateAction.php
 */

namespace App\Rest\Traits\Actions\Logged;

use App\Annotation\RestApiDoc;
use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\UpdateMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait UpdateAction
 *
 * Trait to add 'updateAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\Rest\Traits\Methods\UpdateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Logged
 */
trait UpdateAction
{
    // Traits
    use UpdateMethod;

    /**
     * Update entity with new data, accessible only for 'ROLE_LOGGED' users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_regex%",
     *      },
     *      methods={"PUT"},
     *  )
     *
     * @Security("is_granted('ROLE_LOGGED')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
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
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Request          $request
     * @param RestDtoInterface $restDto
     * @param string           $id
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }
}
