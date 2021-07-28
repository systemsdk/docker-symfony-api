<?php

declare(strict_types=1);

namespace App\Rest\Traits;

/**
 * Trait RestResourceLifeCycles
 *
 * @package App\Rest\Traits
 */
trait RestResourceLifeCycles
{
    use RestResourceFind;
    use RestResourceFindOne;
    use RestResourceFindOneBy;
    use RestResourceCount;
    use RestResourceIds;
    use RestResourceCreate;
    use RestResourceUpdate;
    use RestResourcePatch;
    use RestResourceDelete;
    use RestResourceSave;
}
