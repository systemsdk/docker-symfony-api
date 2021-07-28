<?php

declare(strict_types=1);

namespace App\Security\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslatedAuthenticationFailureHandler
 *
 * @package App\Security\Handler
 */
class TranslatedAuthenticationFailureHandler extends AuthenticationFailureHandler
{
    public function __construct(
        EventDispatcherInterface $dispatcher,
        private TranslatorInterface $translator,
    ) {
        parent::__construct($dispatcher);
    }

    /**
     * {@inheritdoc}
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $event = new AuthenticationFailureEvent(
            $exception,
            new JWTAuthenticationFailureResponse(
                $this->translator->trans('Invalid credentials.', [], 'security')
            )
        );

        $this->dispatcher->dispatch($event);

        return $event->getResponse();
    }
}
