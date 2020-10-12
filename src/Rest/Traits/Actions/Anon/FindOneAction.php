<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/FindOneAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\Rest\Traits\Methods\FindOneMethod;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait FindOneAction
{
    // Traits
    use FindOneMethod;

    /**
     * Find entity, accessible for anonymous users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="object",
     *          example={},
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
