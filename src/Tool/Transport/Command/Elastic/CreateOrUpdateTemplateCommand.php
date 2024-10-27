<?php

declare(strict_types=1);

namespace App\Tool\Transport\Command\Elastic;

use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use App\Tool\Application\Service\Elastic\Interfaces\CreateOrUpdateTemplateServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\Tool
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to create/update index template in Elastic.',
)]
class CreateOrUpdateTemplateCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'elastic:create-or-update-template';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly CreateOrUpdateTemplateServiceInterface $createOrUpdateTemplateService,
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
        $results = $this->createOrUpdateTemplateService->createOrUpdateIndexTemplate();

        if ($input->isInteractive()) {
            $io->success($results);
        }

        return Command::SUCCESS;
    }
}
