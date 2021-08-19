<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions\Authenticated;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\CreateMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for authenticated users.
 *
 * @see \App\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Authenticated
 */
trait CreateAction
{
    use CreateMethod;

    /**
     * Create entity, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @OA\RequestBody(
     *      request="body",
     *      description="object",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"param": "value"},
     *      )
     * )
     *
     * @OA\Response(
     *      response=201,
     *      description="created",
     *      @OA\JsonContent(
     *          type="object",
     *          example={},
     *      ),
     *  )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 403, "message": "Access denied"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
