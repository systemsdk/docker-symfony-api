<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/FindAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\Rest\Traits\Methods\FindMethod;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindAction
 *
 * Trait to add 'findAction' for REST controllers for anonymous users.
 *
 * @see \App\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Anon
 */
trait FindAction
{
    // Traits
    use FindMethod;

    /**
     * Get list of entities, accessible for anonymous users.
     *
     * @Route(
     *     path="",
     *     methods={"GET"},
     *  )
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="array",
     * @SWG\Items(type="string"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
