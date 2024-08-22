<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * @package App\General
 */
trait RestActionBase
{
    /**
     * @param array<int, string> $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function getResourceForMethod(
        Request $request,
        array $allowedHttpMethods
    ): RestResourceInterface|RestSmallResourceInterface {
        // Make sure that we have everything we need to make this work
        $this->validateRestMethod($request, $allowedHttpMethods);

        // Get current resource service
        return $this->getResource();
    }
}
