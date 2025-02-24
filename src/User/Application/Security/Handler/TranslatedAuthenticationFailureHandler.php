<?php

declare(strict_types=1);

namespace App\User\Application\Security\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @package App\User
 */
class TranslatedAuthenticationFailureHandler extends AuthenticationFailureHandler
{
    public function __construct(
        EventDispatcherInterface $dispatcher,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct($dispatcher);
    }

    /**
     * {@inheritdoc}
     *
     * @see https://github.com/lexik/LexikJWTAuthenticationBundle/issues/944
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        /**
         * @psalm-suppress MissingDependency, InvalidArgument
         */
        $event = new AuthenticationFailureEvent(
            $exception,
            new JWTAuthenticationFailureResponse($this->translator->trans('Invalid credentials.', [], 'security'))
        );

        $this->dispatcher->dispatch($event);

        return $event->getResponse() ?? new JWTAuthenticationFailureResponse();
    }
}
