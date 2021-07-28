<?php

declare(strict_types=1);

namespace App\Rest;

use App\Rest\Interfaces\ControllerInterface;
use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\Interfaces\RestResourceInterface;
use App\Rest\Traits\Actions\RestActionBase;
use App\Rest\Traits\RestMethodHelper;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Rest
 *
 * @property ?RestResourceInterface $resource
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

    /**
     * {@inheritdoc}
     */
    public function getResource(): RestResourceInterface
    {
        return $this->resource
               ?? throw new UnexpectedValueException('Resource service not set', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function setResource(RestResourceInterface $resource): self
    {
        $this->resource = $resource;

        return $this;
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
     *
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     */
    public function setResponseHandler(ResponseHandler $responseHandler): self
    {
        $this->responseHandler = $responseHandler;

        return $this;
    }
}
