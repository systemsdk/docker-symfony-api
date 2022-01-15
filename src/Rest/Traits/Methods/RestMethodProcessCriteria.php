<?php

declare(strict_types=1);

namespace App\Rest\Traits\Methods;

use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RestMethodProcessCriteria
 *
 * @package App\Rest\Traits\Methods
 */
trait RestMethodProcessCriteria
{
    /**
     * {@inheritdoc}
     *
     * @param array<int|string, string|array<mixed>> $criteria
     */
    public function processCriteria(array &$criteria, Request $request, string $method): void
    {
    }
}
