<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Admin;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\Role\Domain\Entity\Role;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\General
 */
trait CreateAction
{
    use CreateMethod;

    /**
     * Create entity, accessible only for 'ROLE_ADMIN' users.
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
    #[Route(
        path: '',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
