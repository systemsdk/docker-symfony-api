<?php

declare(strict_types=1);

namespace App\General\Application\Exception\Interfaces;

/**
 * If you want to expose _your_ exception class message as-is on `prod` environment that should
 * implement either this interface or `Symfony\Component\HttpKernel\HttpKernelInterface` to get
 * that message to frontend side - otherwise you will just get `Internal server error.` message
 * with HTTP status 500.
 *
 * If your exception is not returning `code` properly, note that you will get that HTTP status
 * 500 on those - so it's _your_ responsibility to get "proper" status code in your exception
 * class.
 *
 * @package App\General
 */
interface ClientErrorInterface
{
    /**
     * Method to get client response status code.
     */
    public function getStatusCode(): int;
}
