<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Command\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Domain\Entity\UserGroup;
use Closure;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_map;
use function implode;
use function sprintf;

/**
 * @package App\ApiKey
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to list API keys',
)]
class ListApiKeysCommand extends Command
{
    final public const string NAME = 'api-key:list';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly ApiKeyResource $apiKeyResource,
        private readonly RolesServiceInterface $rolesService,
    ) {
        parent::__construct();
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->write("\033\143");
        $headers = [
            'Id',
            'Token',
            'Description',
            'Groups',
            'Roles (inherited)',
        ];
        $io->title('Current API keys');
        $io->table($headers, $this->getRows());

        return 0;
    }

    /**
     * Getter method for formatted API key rows for console table.
     *
     * @throws Throwable
     *
     * @return array<int, string>
     */
    private function getRows(): array
    {
        return array_map($this->getFormatterApiKey(), $this->apiKeyResource->find(orderBy: [
            'token' => 'ASC',
        ]));
    }

    /**
     * Getter method for API key formatter closure. This closure will format single ApiKey entity for console table.
     */
    private function getFormatterApiKey(): Closure
    {
        $userGroupFormatter = static fn (UserGroup $userGroup): string => sprintf(
            '%s (%s)',
            $userGroup->getName(),
            $userGroup->getRole()->getId(),
        );

        return fn (ApiKey $apiToken): array => [
            $apiToken->getId(),
            $apiToken->getToken(),
            $apiToken->getDescription(),
            implode(",\n", $apiToken->getUserGroups()->map($userGroupFormatter)->toArray()),
            implode(",\n", $this->rolesService->getInheritedRoles($apiToken->getRoles())),
        ];
    }
}
