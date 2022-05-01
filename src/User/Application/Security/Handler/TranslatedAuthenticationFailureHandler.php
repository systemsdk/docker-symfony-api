<?php

declare(strict_types=1);

namespace App\User\Application\Security\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslatedAuthenticationFailureHandler
 *
 * @package App\User
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
     * @see https://github.com/lexik/LexikJWTAuthenticationBundle/issues/944
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        /**
         * @psalm-suppress MissingDependency, InvalidArgument
         */
        $event = new AuthenticationFailureEvent(
            $exception,
            new JWTAuthenticationFailureResponse( // @phpstan-ignore-line
                $this->translator->trans('Invalid credentials.', [], 'security')
            )
        );

        $this->dispatcher->dispatch($event);

        return $event->getResponse();
    }
}