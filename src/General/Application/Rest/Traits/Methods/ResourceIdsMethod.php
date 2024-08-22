<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits\Methods;

use App\General\Application\Rest\Traits\RestResourceIds as IdsLifeCycle;

/**
 * @package App\General
 */
trait ResourceIdsMethod
{
    use IdsLifeCycle;

    /**
     * {@inheritdoc}
     */
    public function getIds(?array $criteria = null, ?array $search = null, ?string $entityManagerName = null): array
    {
        $criteria ??= [];
        $search ??= [];
        // Before callback method call
        $this->beforeIds($criteria, $search);
        // Fetch data
        $ids = $this->getRepository()->findIds($criteria, $search, $entityManagerName);
        // After callback method call
        $this->afterIds($ids, $criteria, $search);

        return $ids;
    }
}
