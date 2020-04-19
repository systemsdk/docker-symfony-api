<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/RestMethodProcessCriteria.php
 */

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
     */
    public function processCriteria(array &$criteria, Request $request, string $method): void
    {
    }
}
