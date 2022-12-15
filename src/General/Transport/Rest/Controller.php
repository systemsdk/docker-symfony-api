<?php

declare(strict_types=1);

namespace App\General\Transport\Rest;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use App\General\Transport\Rest\Interfaces\ControllerInterface;
use App\General\Transport\Rest\Interfaces\ResponseHandlerInterface;
use App\General\Transport\Rest\Traits\Actions\RestActionBase;
use App\General\Transport\Rest\Traits\RestMethodHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\General
 *
 * @property RestResourceInterface|RestSmallResourceInterface|null $resource
 */
abstract class Controller implements ControllerInterface
{
    use RestActionBase;
    use RestMethodHelper;

    public const ACTION_COUNT = 'countAction';
    public const ACTION_CREATE = 'createAction';
    public const ACTION_DELETE = 'deleteAction';
    public const ACTION_FIND = 'findAction';
    public const ACTION_FIND_ONE = 'findOneAction';
    public const ACTION_IDS = 'idsAction';
    public const ACTION_PATCH = 'patchAction';
    public const ACTION_UPDATE = 'updateAction';

    public const METHOD_COUNT = 'countMethod';
    public const METHOD_CREATE = 'createMethod';
    public const METHOD_DELETE = 'deleteMethod';
    public const METHOD_FIND = 'findMethod';
    public const METHOD_FIND_ONE = 'findOneMethod';
    public const METHOD_IDS = 'idsMethod';
    public const METHOD_PATCH = 'patchMethod';
    public const METHOD_UPDATE = 'updateMethod';

    protected ?ResponseHandlerInterface $responseHandler = null;

    public function __construct(
        protected readonly RestResourceInterface|RestSmallResourceInterface $resource
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getResource(): RestResourceInterface|RestSmallResourceInterface
    {
        return $this->resource
               ?? throw new UnexpectedValueException('Resource service not set', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * {@inheritdoc}
     */
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
    public function setResponseHandler(ResponseHandler $responseHandler): static
    {
        $this->responseHandler = $responseHandler;

        return $this;
    }
}
