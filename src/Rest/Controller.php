<?php
declare(strict_types = 1);
/**
 * /src/Rest/Controller.php
 */

namespace App\Rest;

use App\Rest\Interfaces\ControllerInterface;
use App\Rest\Interfaces\RestResourceInterface;
use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\Traits\Actions\RestActionBase;
use App\Rest\Traits\RestMethodHelper;
use UnexpectedValueException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 *
 * @package App\Rest
 */
abstract class Controller implements ControllerInterface
{
    // Traits
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

    protected ?RestResourceInterface $resource = null;
    protected ?ResponseHandlerInterface $responseHandler = null;

    /** @inheritDoc */
    public function setResource(RestResourceInterface $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @throws UnexpectedValueException
     *
     * @return RestResourceInterface
     */
    public function getResource(): RestResourceInterface
    {
        if (!$this->resource instanceof RestResourceInterface) {
            throw new UnexpectedValueException('Resource service not set', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->resource;
    }

    /**
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     *
     * @param ResponseHandler $responseHandler
     *
     * @return self
     */
    public function setResponseHandler(ResponseHandler $responseHandler): self
    {
        $this->responseHandler = $responseHandler;

        return $this;
    }

    /**
     * @throws UnexpectedValueException
     *
     * @return ResponseHandlerInterface
     */
    public function getResponseHandler(): ResponseHandlerInterface
    {
        if (!$this->responseHandler instanceof ResponseHandlerInterface) {
            throw new UnexpectedValueException('ResponseHandler service not set', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responseHandler;
    }
}