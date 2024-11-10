<?php

declare(strict_types=1);

namespace App\User\Transport\Command\User;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\User\Application\DTO\UserGroup\UserGroupPatch as UserGroupDto;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup as UserGroupEntity;
use App\User\Transport\Form\Type\Console\UserGroupType;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\User
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to edit existing user group',
)]
class EditUserGroupCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'user:edit-group';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly UserGroupResource $userGroupResource,
        private readonly UserHelper $userHelper,
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
        $io = $this->getSymfonyStyle($input, $output);
        $userGroup = $this->userHelper->getUserGroup($io, 'Which user group you want to edit?');
        $message = $userGroup instanceof UserGroupEntity ? $this->updateUserGroup($input, $output, $userGroup) : null;

        if ($input->isInteractive()) {
            $io->success($message ?? 'Nothing changed - have a nice day');
        }

        return 0;
    }

    /**
     * Method to update specified user group entity via specified form.
     *
     * @throws Throwable
     */
    protected function updateUserGroup(
        InputInterface $input,
        OutputInterface $output,
        UserGroupEntity $userGroup,
    ): string {
        // Load entity to DTO
        $dtoLoaded = new UserGroupDto();
        $dtoLoaded->load($userGroup);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserGroupDto $dtoEdit */
        $dtoEdit = $helper->interactUsingForm(UserGroupType::class, $input, $output, [
            'data' => $dtoLoaded,
        ]);
        // Patch user group
        $this->userGroupResource->patch($userGroup->getId(), $dtoEdit);

        return 'User group updated - have a nice day';
    }
}
