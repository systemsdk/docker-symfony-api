<?php
declare(strict_types = 1);
/**
 * /src/Form/Type/Interfaces/FormTypeLabelInterface.php
 */

namespace App\Form\Type\Interfaces;

/**
 * Interface FormTypeLabelInterface
 *
 * @package App\Form\Type\Interfaces
 */
interface FormTypeLabelInterface
{
    public const LABEL = 'label';
    public const REQUIRED = 'required';
    public const EMPTY_DATA = 'empty_data';
    public const TYPE = 'type';
    public const FIRST_NAME = 'first_name';
    public const FIRST_OPTIONS = 'first_options';
    public const SECOND_NAME = 'second_name';
    public const SECOND_OPTIONS = 'second_options';
    public const CHOICES = 'choices';
}
