<?php

declare(strict_types=1);

namespace App\User\Transport\EventSubscriber;

use App\User\Application\Security\UserTypeIdentification;
use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Blameable\BlameableListener;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @package App\User
 */
class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BlameableListener $blameableListener,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    #[Override]
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
