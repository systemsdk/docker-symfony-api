<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/MethodHelper.php
 */

namespace App\Rest\Traits\Actions;

use App\Rest\Interfaces\RestResourceInterface;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use UnexpectedValueException;

/**
 * Trait MethodHelper
 *
 * @package App\Rest\Traits\Methods
 */
trait RestActionBase
{
    /**
     * @return RestResourceInterface
     *
     * @throws UnexpectedValueException
     */
    abstract public function getResource(): RestResourceInterface;

    /**
     * Method to validate REST trait method.
     *
     * @param Request  $request
     * @param string[] $allowedHttpMethods
     *
     * @throws LogicException
     * @throws MethodNotAllowedHttpException
     */
    abstract public function validateRestMethod(Request $request, array $allowedHttpMethods): void;

    /**
     * @param Request                  $request
     * @param array|array<int, string> $allowedHttpMethods
     *
     * @throws Throwable
     *
     * @return RestResourceInterface
     */
    public function validateRestMethodAndGetResource(Request $request, array $allowedHttpMethods): RestResourceInterface
    {
        // Make sure that we have everything we need to make this work
        $this->validateRestMethod($request, $allowedHttpMethods);

        // Get current resource service
        return $this->getResource();
    }
}
