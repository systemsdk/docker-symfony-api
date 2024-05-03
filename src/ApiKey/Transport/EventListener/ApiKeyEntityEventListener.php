<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\EventListener;

use App\ApiKey\Domain\Entity\ApiKey;
use App\Tool\Domain\Exception\Crypt\Exception;
use App\Tool\Domain\Service\Crypt\Interfaces\OpenSslCryptServiceInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

use function strlen;

/**
 * @package App\ApiKey
 */
class ApiKeyEntityEventListener
{
    public function __construct(
        private readonly bool $apiKeyTokenOpenSslEncrypt,
        private readonly string $apiKeyTokenHashAlgo,
        private readonly OpenSslCryptServiceInterface $openSslCryptService,
    ) {
    }

    /**
     * Doctrine lifecycle event for 'prePersist' event.
     *
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->process($event, 'encrypt');
    }

    /**
     * Doctrine lifecycle event for 'postPersist' event.
     *
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->process($event, 'decrypt');
    }

    /**
     * Doctrine lifecycle event for 'preUpdate' event.
     *
     * @throws Exception
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->process($event, 'encrypt');
    }

    /**
     * Doctrine lifecycle event for 'postUpdate' event.
     *
     * @throws Exception
     */
    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->process($event, 'decrypt');
    }

    /**
     * Doctrine lifecycle event for 'postLoad' event.
     *
     * @throws Exception
     */
    public function postLoad(LifecycleEventArgs $event): void
    {
        $this->process($event, 'decrypt');
    }

    /**
     * @throws Exception
     */
    private function process(LifecycleEventArgs $event, string $action): void
    {
        $apiKey = $event->getObject();

        if ($this->apiKeyTokenOpenSslEncrypt && $apiKey instanceof ApiKey) {
            $action === 'encrypt' ? $this->encryptToken($apiKey) : $this->decryptToken($apiKey);
        }
    }

    /**
     * @throws Exception
     */
    private function encryptToken(ApiKey $apiKey): void
    {
        $token = $apiKey->getToken();

        if ($token === '' || strlen($token) !== ApiKey::TOKEN_LENGTH) {
            return;
        }

        $data = $this->openSslCryptService->encrypt($token);
        $apiKey
            ->setToken($data['data'])
            ->setTokenParameters($data['params'])
            ->setTokenHash(hash($this->apiKeyTokenHashAlgo, $token));
    }

    /**
     * @throws Exception
     */
    private function decryptToken(ApiKey $apiKey): void
    {
        $token = $apiKey->getToken();
        /** @var array{iv: string, tag: string}|null $tokenParams */
        $tokenParams = $apiKey->getTokenParameters();

        if ($token === '' || $tokenParams === null || strlen($token) === ApiKey::TOKEN_LENGTH) {
            return;
        }

        $decryptedToken = $this->openSslCryptService->decrypt([
            'data' => $token,
            'params' => $tokenParams,
        ]);
        $apiKey->setToken($decryptedToken);
    }
}
