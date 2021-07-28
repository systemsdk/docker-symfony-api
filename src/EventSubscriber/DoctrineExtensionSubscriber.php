<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Security\UserTypeIdentification;
use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Blameable\BlameableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class DoctrineExtensionSubscriber
 *
 * @package App\EventSubscriber
 */
class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private BlameableListener $blameableListener,
        private UserTypeIdentification $userTypeIdentification,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function onKernelRequest(): void
    {
        $user = $this->userTypeIdentification->getUser();

        if ($user !== null) {
            $this->blameableListener->setUserValue($user);
        }
    }
}
