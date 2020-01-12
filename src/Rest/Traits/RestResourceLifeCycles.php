<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Resource.php
 */

namespace App\Rest\Traits;

/**
 * Trait Resource
 *
 * @package App\Rest\Traits
 */
trait RestResourceLifeCycles
{
    // Traits
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
