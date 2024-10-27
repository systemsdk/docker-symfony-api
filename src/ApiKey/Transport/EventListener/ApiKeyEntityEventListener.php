<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\EventListener;

use App\ApiKey\Application\Service\Crypt\Interfaces\OpenSslCryptApiKeyServiceInterface;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Tool\Domain\Exception\Crypt\Exception;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * @package App\ApiKey
 */
class ApiKeyEntityEventListener
{
    public function __construct(
        private readonly OpenSslCryptApiKeyServiceInterface $openSslCryptApiKeyService,
        private readonly bool $apiKeyTokenOpenSslEncrypt,
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
            $action === 'encrypt'
                ? $this->openSslCryptApiKeyService->encryptToken($apiKey)
                : $this->openSslCryptApiKeyService->decryptToken($apiKey);
        }
    }
}
