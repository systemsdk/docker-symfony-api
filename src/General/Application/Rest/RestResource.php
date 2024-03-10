<?php

declare(strict_types=1);

namespace App\General\Application\Rest;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Application\Rest\Traits\Methods;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;

/**
 * @package App\General
 */
abstract class RestResource implements RestResourceInterface
{
    use Traits\RestResourceBaseMethods;
    use Methods\ResourceCountMethod;
    use Methods\ResourceCreateMethod;
    use Methods\ResourceDeleteMethod;
    use Methods\ResourceFindMethod;
    use Methods\ResourceFindOneByMethod;
    use Methods\ResourceFindOneMethod;
    use Methods\ResourceIdsMethod;
    use Methods\ResourcePatchMethod;
    use Methods\ResourceSaveMethod;
    use Methods\ResourceUpdateMethod;

    public function __construct(
        protected readonly BaseRepositoryInterface $repository,
    ) {
    }
}
