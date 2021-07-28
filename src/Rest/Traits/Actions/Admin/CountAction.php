<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Admin;

use App\Rest\Traits\Methods\CountMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"count": "1"},
     *          @OA\Property(property="count", type="integer"),
     *      ),
     *  )
     * @OA\Response(
     *      response=403,
     *      description="Access denied",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 403, "message": "Access denied"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
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
