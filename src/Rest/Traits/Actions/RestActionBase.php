<?php

declare(strict_types=1);

namespace App\Rest\Traits\Actions;

use App\Rest\Interfaces\RestResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Trait RestActionBase
 *
 * @package App\Rest\Traits\Methods
 */
trait RestActionBase
{
    /**
     * @param array<int, string> $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function getResourceForMethod(Request $request, array $allowedHttpMethods): RestResourceInterface
    {
        // Make sure that we have everything we need to make this work
        $this->validateRestMethod($request, $allowedHttpMethods);

        // Get current resource service
        return $this->getResource();
    }
}
