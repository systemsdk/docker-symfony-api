<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/IdsAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\IdsMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait IdsAction
 *
 * Trait to add 'idsAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait IdsAction
{
    // Traits
    use IdsMethod;

    /**
     * Find ids list, accessible for anonymous users.
     *
     * @Route(
     *     path="/ids",
     *     methods={"GET"},
     *  )
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(type="string"),
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
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
