<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use Symfony\Component\HttpFoundation\Request;

/**
 * @package App\General
 */
trait RestMethodProcessCriteria
{
    /**
     * {@inheritDoc}
     *
     * @param array<string, string|array<mixed>> $criteria
     */
    public function processCriteria(array &$criteria, Request $request, string $method): void
    {
    }
}
