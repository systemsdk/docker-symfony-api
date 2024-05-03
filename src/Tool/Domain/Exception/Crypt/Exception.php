<?php

declare(strict_types=1);

namespace App\Tool\Domain\Exception\Crypt;

use App\General\Domain\Exception\BaseTranslatableException;

/**
 * @package App\Tool
 */
class Exception extends BaseTranslatableException
{
    public function getDomain(): ?string
    {
        return 'crypt';
    }
}
