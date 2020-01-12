<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/CountAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\CountMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait CountAction
 *
 * Trait to add 'countAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait CountAction
{
    // Traits
    use CountMethod;

    /**
     * Count entities, accessible for anonymous users.
     *
     * @Route(
     *     path="/count",
     *     methods={"GET"},
     *  )
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
