<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Command\ApiKey;

use App\ApiKey\Application\DTO\ApiKey\ApiKey as ApiKeyDto;
use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey as ApiKeyEntity;
use App\ApiKey\Transport\Form\Type\Console\ApiKeyType;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package App\ApiKey
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to edit existing API key',
)]
class EditApiKeyCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'api-key:edit';

    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly ApiKeyResource $apiKeyResource,
        private readonly ApiKeyHelper $apiKeyHelper,
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
        $apiKey = $this->apiKeyHelper->getApiKey($io, 'Which API key you want to edit?');
        $message = $apiKey instanceof ApiKeyEntity ? $this->updateApiKey($input, $output, $apiKey) : null;

        if ($input->isInteractive()) {
            $io->success($message ?? ['Nothing changed - have a nice day']);
        }

        return 0;
    }

    /**
     * Method to update specified API key via specified form.
     *
     * @return array<int, string>
     *
     * @throws Throwable
     */
    private function updateApiKey(InputInterface $input, OutputInterface $output, ApiKeyEntity $apiKey): array
    {
        // Load entity to DTO
        $dtoLoaded = new ApiKeyDto();
        $dtoLoaded->load($apiKey);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var ApiKeyDto $dtoEdit */
        $dtoEdit = $helper->interactUsingForm(ApiKeyType::class, $input, $output, [
            'data' => $dtoLoaded,
        ]);
        // Patch API key
        $this->apiKeyResource->patch($apiKey->getId(), $dtoEdit);

        return $this->apiKeyHelper->getApiKeyMessage('API key updated - have a nice day', $apiKey);
    }
}
