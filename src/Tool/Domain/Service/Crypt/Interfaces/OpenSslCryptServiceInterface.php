<?php

declare(strict_types=1);

namespace App\Tool\Domain\Service\Crypt\Interfaces;

/**
 * @package App\Tool
 */
interface OpenSslCryptServiceInterface extends CryptServiceInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array{data: non-empty-string, params: array{iv: string, tag: string}}
     */
    public function encrypt(string $textForEncrypt): array;

    /**
     * {@inheritdoc}
     *
     * @param array{data: string, params: array{iv: string, tag: mixed}} $dataForDecrypt
     */
    public function decrypt(array $dataForDecrypt): string;
}
