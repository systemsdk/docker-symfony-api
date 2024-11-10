<?php

declare(strict_types=1);

namespace App\General\Transport\ValueResolver;

use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Generator;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Throwable;

/**
 * Example how to use this within your controller;
 *
 *  #[Route(path: 'some-path')]
 *  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
 *  public function someMethod(\App\User\Domain\Entity\User $loggedInUser): Response
 *  {
 *      ...
 *  }
 *
 * This will automatically convert your security user to actual User entity that
 * you can use within your controller as you like.
 *
 * @package App\General
 */
class LoggedInUserValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly UserTypeIdentification $userService,
    ) {
    }

    /**
     * @throws MissingTokenException
     */
    public function supports(ArgumentMetadata $argument): bool
    {
        $output = false;

        // only security user implementations are supported
        if ($argument->getName() === 'loggedInUser' && $argument->getType() === User::class) {
            $securityUser = $this->userService->getSecurityUser();

            if ($securityUser === null && $argument->isNullable() === false) {
                throw new MissingTokenException('JWT Token not found');
            }

            $output = true;
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     *
     * @return Generator<User|null>
     *
     * @throws Throwable
     */
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($argument)) {
            return [];
        }

        yield $this->userService->getUser();
    }
}
