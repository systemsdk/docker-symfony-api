<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/UpdateAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\UpdateMethod;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait UpdateAction
 *
 * Trait to add 'updateAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\UpdateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait UpdateAction
{
    // Traits
    use UpdateMethod;

    /**
     * Update entity with new data, accessible for anonymous users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"PUT"},
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
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }
}
