<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Authenticated;

use App\General\Transport\Rest\Traits\Methods\FindMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Throwable;

/**
 * Trait FindAction
 *
 * Trait to add 'findAction' for REST controllers for authenticated users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\General
 */
trait FindAction
{
    use FindMethod;

    /**
     * Get list of entities, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(type="string"),
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
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
