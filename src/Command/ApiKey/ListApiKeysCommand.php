<?php
declare(strict_types = 1);
/**
 * /src/Command/ApiKey/ListApiKeysCommand.php
 */

namespace App\Command\ApiKey;

use Symfony\Component\Console\Command\Command;
use App\Resource\ApiKeyResource;
use App\Security\RolesService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\UserGroup;
use App\Entity\ApiKey;
use Closure;
use Symfony\Component\Console\Exception\LogicException;
use Throwable;

/**
 * Class ListApiKeysCommand
 *
 * @package App\Command\ApiKey
 */
class ListApiKeysCommand extends Command
{
    private ApiKeyResource $apiKeyResource;
    private RolesService $rolesService;
    private SymfonyStyle $io;


    /**
     * Constructor
     *
     * @param ApiKeyResource $apiKeyResource
     * @param RolesService   $rolesService
     *
     * @throws LogicException
     */
    public function __construct(ApiKeyResource $apiKeyResource, RolesService $rolesService)
    {
        parent::__construct('api-key:list');

        $this->apiKeyResource = $apiKeyResource;
        $this->rolesService = $rolesService;

        $this->setDescription('Console command to list API keys');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->write("\033\143");
        $headers = [
            'Id',
            'Token',
            'Description',
            'Groups',
            'Roles (inherited)',
        ];
        $this->io->title('Current API keys');
        $this->io->table($headers, $this->getRows());

        return 0;
    }

    /**
     * Getter method for formatted API key rows for console table.
     *
     * @throws Throwable
     *
     * @return array
     */
    private function getRows(): array
    {
        return array_map($this->getFormatterApiKey(), $this->apiKeyResource->find(null, ['token' => 'ASC']));
    }

    /**
     * Getter method for API key formatter closure. This closure will format single ApiKey entity for console table.
     *
     * @return Closure
     */
    private function getFormatterApiKey(): Closure
    {
        $userGroupFormatter = static function (UserGroup $userGroup): string {
            return sprintf(
                '%s (%s)',
                $userGroup->getName(),
                $userGroup->getRole()->getId()
            );
        };

        return function (ApiKey $apiToken) use ($userGroupFormatter): array {
            return [
                $apiToken->getId(),
                $apiToken->getToken(),
                $apiToken->getDescription(),
                implode(",\n", $apiToken->getUserGroups()->map($userGroupFormatter)->toArray()),
                implode(",\n", $this->rolesService->getInheritedRoles($apiToken->getRoles())),
            ];
        };
    }
}
