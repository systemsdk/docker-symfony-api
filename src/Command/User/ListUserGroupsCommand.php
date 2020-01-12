<?php
declare(strict_types = 1);
/**
 * /src/Command/User/ListUserGroupsCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\StyleSymfony;
use App\Resource\UserGroupResource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\LogicException;
use App\Entity\User;
use App\Entity\UserGroup;
use Closure;
use Throwable;

/**
 * Class ListUserGroupsCommand
 *
 * @package App\Command\User
 */
class ListUserGroupsCommand extends Command
{
    // Traits
    use StyleSymfony;

    private UserGroupResource $userGroupResource;


    /**
     * Constructor
     *
     * @param UserGroupResource $userGroupResource
     *
     * @throws LogicException
     */
    public function __construct(UserGroupResource $userGroupResource)
    {
        parent::__construct('user:list-groups');

        $this->userGroupResource = $userGroupResource;

        $this->setDescription('Console command to list user groups');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        $headers = [
            'Id',
            'Name',
            'Role',
            'Users',
        ];
        $io->title('Current user groups');
        $io->table($headers, $this->getRows());

        return 0;
    }

    /**
     * Getter method for formatted user group rows for console table.
     *
     * @throws Throwable
     *
     * @return array
     */
    private function getRows(): array
    {
        return array_map($this->getFormatterUserGroup(), $this->userGroupResource->find(null, ['name' => 'ASC']));
    }

    /**
     * Getter method for user group formatter closure. This closure will format single UserGroup entity for console
     * table.
     *
     * @return Closure
     */
    private function getFormatterUserGroup(): Closure
    {
        $userFormatter = static function (User $user): string {
            return sprintf(
                '%s %s <%s>',
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail()
            );
        };

        return static function (UserGroup $userGroup) use ($userFormatter): array {
            return [
                $userGroup->getId(),
                $userGroup->getName(),
                $userGroup->getRole()->getId(),
                implode(",\n", $userGroup->getUsers()->map($userFormatter)->toArray()),
            ];
        };
    }
}
