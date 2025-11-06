<?php

declare(strict_types=1);

namespace App\General\Transport\Rest;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use App\General\Transport\Rest\Interfaces\ControllerInterface;
use App\General\Transport\Rest\Interfaces\ResponseHandlerInterface;
use App\General\Transport\Rest\Traits\Actions\RestActionBase;
use App\General\Transport\Rest\Traits\RestMethodHelper;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use UnexpectedValueException;

/**
 * @package App\General
 *
 * @property RestResourceInterface|RestSmallResourceInterface|null $resource
 */
abstract class Controller implements ControllerInterface
{
    use RestActionBase;
    use RestMethodHelper;

    public const string ACTION_COUNT = 'countAction';
    public const string ACTION_CREATE = 'createAction';
    public const string ACTION_DELETE = 'deleteAction';
    public const string ACTION_FIND = 'findAction';
    public const string ACTION_FIND_ONE = 'findOneAction';
    public const string ACTION_IDS = 'idsAction';
    public const string ACTION_PATCH = 'patchAction';
    public const string ACTION_UPDATE = 'updateAction';

    public const string METHOD_COUNT = 'countMethod';
    public const string METHOD_CREATE = 'createMethod';
    public const string METHOD_DELETE = 'deleteMethod';
    public const string METHOD_FIND = 'findMethod';
    public const string METHOD_FIND_ONE = 'findOneMethod';
    public const string METHOD_IDS = 'idsMethod';
    public const string METHOD_PATCH = 'patchMethod';
    public const string METHOD_UPDATE = 'updateMethod';

    protected ?ResponseHandlerInterface $responseHandler = null;

    public function __construct(
        protected readonly RestResourceInterface|RestSmallResourceInterface $resource
    ) {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getResource(): RestResourceInterface|RestSmallResourceInterface
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getResponseHandler(): ResponseHandlerInterface
    {
        return $this->responseHandler
               ?? throw new UnexpectedValueException(
                   'ResponseHandler service not set',
                   Response::HTTP_INTERNAL_SERVER_ERROR
               );
    }

    /**
     * {@inheritdoc}
     */
    #[Required]
    #[Override]
    public function setResponseHandler(ResponseHandler $responseHandler): static
    {
        $this->responseHandler = $responseHandler;

        return $this;
    }
}
