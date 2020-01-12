<?php
declare(strict_types = 1);
/**
 * /src/Rest/Interfaces/SearchTermInterface.php
 */

namespace App\Rest\Interfaces;

/**
 * Interface SearchTermInterface
 *
 * @package App\Rest\Interfaces
 */
interface SearchTermInterface
{
    // Used OPERAND constants
    public const OPERAND_OR = 'or';
    public const OPERAND_AND = 'and';

    // Used MODE constants
    public const MODE_STARTS_WITH = 1;
    public const MODE_ENDS_WITH = 2;
    public const MODE_FULL = 3;
}
