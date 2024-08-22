<?php

declare(strict_types=1);

namespace App\General\Transport\Form\Type\Traits;

use Symfony\Component\Form\FormBuilderInterface;

use function call_user_func_array;

/**
 * @package App\General
 */
trait AddBasicFieldToForm
{
    /**
     * @param array<int, array<int, mixed>> $fields
     */
    protected function addBasicFieldToForm(FormBuilderInterface $builder, array $fields): void
    {
        foreach ($fields as $params) {
            call_user_func_array($builder->add(...), $params);
        }
    }
}
