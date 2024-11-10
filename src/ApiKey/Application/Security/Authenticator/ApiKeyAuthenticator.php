<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Security\Authenticator;

use App\ApiKey\Application\Security\Provider\ApiKeyUserProvider;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Throwable;

use function preg_match;

/**
 * @package App\ApiKey
 */
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly ApiKeyUserProvider $apiKeyUserProvider,
    ) {
    }

    #[Override]
    public function supports(Request $request): ?bool
    {
        return $this->getToken($request) !== '';
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public function authenticate(Request $request): Passport
    {
        $token = $this->getToken($request);
        $apiKey = $this->apiKeyUserProvider->getApiKeyForToken($token);

        if ($apiKey === null) {
            throw new UserNotFoundException('API key not found');
        }

        return new SelfValidatingPassport(new UserBadge($token));
    }

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Invalid ApiKey',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    private function getToken(Request $request): string
    {
        preg_match('#^ApiKey (\w+)$#', $request->headers->get('Authorization', ''), $matches);

        return $matches[1] ?? '';
    }
}
