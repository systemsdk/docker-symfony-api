<?php
declare(strict_types = 1);
/**
 * /src/Annotation/RestApiDoc.php
 */

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class RestApiDoc
 *
 * @Annotation
 * @Annotation\Target({"CLASS", "METHOD"})
 *
 * @package App\Annotation
 */
class RestApiDoc
{
    public bool $disabled = false;
}
