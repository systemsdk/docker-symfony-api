<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Transport\Rest\Traits\Methods\CountMethod;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CountAction
 *
 * Trait to add 'countAction' for REST controllers for anonymous users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\General
 */
trait CountAction
{
    use CountMethod;

    /**
     * Count entities, accessible for anonymous users.
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"count": "1"},
     *         @OA\Property(property="count", type="integer"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/count',
        methods: [Request::METHOD_GET],
    )]
    public function countAction(Request $request): Response
    {
        return $this->countMethod($request);
    }
}
