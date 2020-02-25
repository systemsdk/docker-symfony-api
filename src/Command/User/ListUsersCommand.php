<?php
declare(strict_types = 1);
/**
 * /src/Command/User/ListUsersCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\StyleSymfony;
use App\Resource\UserResource;
use App\Security\RolesService;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Closure;
use App\Entity\UserGroup;
use App\Entity\User;
use Throwable;

/**
 * Class ListUsersCommand
 *
 * @package App\Command\User
 */
class ListUsersCommand extends Command
{
    // Traits
    use StyleSymfony;

    private UserResource $userResource;
    private RolesService $roles;


    /**
     * Constructor
     *
     * @param UserResource $userResource
     * @param RolesService $roles
     *
     * @throws LogicException
     */
    public function __construct(UserResource $userResource, RolesService $roles)
    {
        parent::__construct('user:list');

        $this->userResource = $userResource;
        $this->roles = $roles;

        $this->setDescription('Console command to list users');
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
            'Username',
            'Email',
            'Full name',
            'Roles (inherited)',
            'Groups',
        ];
        $io->title('Current users');
        $io->table($headers, $this->getRows());

        return 0;
    }

    /**
     * Getter method for formatted user rows for console table.
     *
     * @throws Throwable
     *
     * @return array
     */
    private function getRows(): array
    {
        return array_map($this->getFormatterUser(), $this->userResource->find(null, ['username' => 'ASC']));
    }

    /**
     * Getter method for user formatter closure. This closure will format single User entity for console table.
     *
     * @return Closure
     */
    private function getFormatterUser(): Closure
    {
        $userGroupFormatter = fn (UserGroup $userGroup): string => sprintf(
            '%s (%s)',
            $userGroup->getName(),
            $userGroup->getRole()->getId()
        );

        return fn (User $user): array => [
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getFirstName() . ' ' . $user->getLastName(),
            implode(",\n", $this->roles->getInheritedRoles($user->getRoles())),
            implode(",\n", $user->getUserGroups()->map($userGroupFormatter)->toArray()),
        ];
    }
}
