<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Command\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey as ApiKeyEntity;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use Closure;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_map;
use function implode;
use function sprintf;

/**
 * @package App\ApiKey
 */
class ApiKeyHelper
{
    public function __construct(
        private readonly ApiKeyResource $apiKeyResource,
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Method to get API key entity. Also note that this may return a null in cases that user do not want to make any
     * changes to API keys.
     *
     * @throws Throwable
     */
    public function getApiKey(SymfonyStyle $io, string $question): ?ApiKeyEntity
    {
        $found = false;
        $apiKey = null;

        while ($found !== true) {
            $apiKey = $this->getApiKeyEntity($io, $question);

            if (!$apiKey instanceof ApiKeyEntity) {
                break;
            }

            $message = sprintf(
                'Is this the correct API key \'[%s] [%s] %s\'?',
                $apiKey->getId(),
                $apiKey->getToken(),
                $apiKey->getDescription(),
            );

            $found = $io->confirm($message, false);
        }

        return $apiKey ?? null;
    }

    /**
     * Helper method to get "normalized" message for API key. This is used on following cases:
     *  - User changes API key token
     *  - User creates new API key
     *  - User modifies API key
     *  - User removes API key
     *
     * @return array<int, string>
     */
    public function getApiKeyMessage(string $message, ApiKeyEntity $apiKey): array
    {
        return [
            $message,
            sprintf(
                "GUID:  %s\nToken: %s",
                $apiKey->getId(),
                $apiKey->getToken(),
            ),
        ];
    }

    /**
     * Method to list ApiKeys where user can select desired one.
     *
     * @throws Throwable
     */
    private function getApiKeyEntity(SymfonyStyle $io, string $question): ?ApiKeyEntity
    {
        $choices = [];
        array_map($this->getApiKeyIterator($choices), $this->apiKeyResource->find(orderBy: [
            'token' => 'ASC',
        ]));
        $choices['Exit'] = 'Exit command';

        return $this->apiKeyResource->findOne((string)$io->choice($question, $choices));
    }

    /**
     * Method to return ApiKeyIterator closure. This will format ApiKey entities for choice list.
     *
     * @param array<string, string> $choices
     */
    private function getApiKeyIterator(array &$choices): Closure
    {
        return function (ApiKeyEntity $apiKey) use (&$choices): void {
            $choices[$apiKey->getId()] = sprintf(
                '[Token: %s] %s - Roles: %s',
                $apiKey->getToken(),
                $apiKey->getDescription(),
                implode(', ', $this->rolesService->getInheritedRoles($apiKey->getRoles())),
            );
        };
    }
}
