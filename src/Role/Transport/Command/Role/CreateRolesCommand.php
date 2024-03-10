<?php

declare(strict_types=1);

namespace App\Role\Transport\Command\Role;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Role\Application\Security\RolesService;
use App\Role\Domain\Entity\Role;
use App\Role\Domain\Repository\Interfaces\RoleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_map;
use function array_sum;
use function sprintf;

/**
 * @package App\Role
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create roles to database',
)]
class CreateRolesCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'user:create-roles';

    /**
     * Constructor
     *
     * @param \App\Role\Infrastructure\Repository\RoleRepository $roleRepository
     *
     * @throws LogicException
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RolesService $rolesService,
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        $created = array_sum(
            array_map(
                fn (string $role): int => $this->createRole($role),
                $this->rolesService->getRoles(),
            ),
        );
        $this->entityManager->flush();
        $removed = $this->clearRoles($this->rolesService->getRoles());

        if ($input->isInteractive()) {
            $message = sprintf(
                'Created total of %d role(s) and removed %d role(s) - have a nice day',
                $created,
                $removed,
            );
            $io->success($message);
        }

        return 0;
    }

    /**
     * Method to check if specified role exists on database and if not create and persist it to database.
     *
     * @throws Throwable
     */
    private function createRole(string $role): int
    {
        $output = 0;

        if ($this->roleRepository->find($role) === null) {
            $entity = new Role($role);
            $entity->setDescription($this->rolesService->getRoleLabel($role));
            $this->entityManager->persist($entity);
            $output = 1;
        }

        return $output;
    }

    /**
     * Method to clean existing roles from database that does not really exists.
     *
     * @param array<int, string> $roles
     */
    private function clearRoles(array $roles): int
    {
        return (int)$this->roleRepository->createQueryBuilder('role')
            ->delete()
            ->where('role.id NOT IN(:roles)')
            ->setParameter(':roles', $roles)
            ->getQuery()
            ->execute();
    }
}
