<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\Rest\Traits\RestResourceCount as CountLifeCycle;

/**
 * @package App\General
 */
trait ResourceCountMethod
{
    use CountLifeCycle;

    /**
     * {@inheritdoc}
     */
    public function count(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): int
    {
        $criteria ??= [];
        $search ??= [];
        // Before callback method call
        $this->beforeCount($criteria, $search);
        $count = $this->getRepository()->countAdvanced($criteria, $search, $entityManagerName);
        // After callback method call
        $this->afterCount($criteria, $search, $count);

        return $count;
    }
}
