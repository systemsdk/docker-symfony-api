<?php

declare(strict_types=1);

namespace App\ApiKey\Transport\Command\ApiKey;

use App\ApiKey\Application\Resource\ApiKeyResource;
use App\ApiKey\Domain\Entity\ApiKey as ApiKeyEntity;
use App\General\Transport\Command\Traits\SymfonyStyleTrait;
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
    description: 'Console command to remove existing API key',
)]
class RemoveApiKeyCommand extends Command
{
    use SymfonyStyleTrait;

    final public const string NAME = 'api-key:remove';

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
        $apiKey = $this->apiKeyHelper->getApiKey($io, 'Which API key you want to remove?');
        $message = $apiKey instanceof ApiKeyEntity ? $this->delete($apiKey) : null;

        if ($input->isInteractive()) {
            $io->success($message ?? ['Nothing changed - have a nice day']);
        }

        return 0;
    }

    /**
     * @throws Throwable
     *
     * @return array<int, string>
     */
    private function delete(ApiKeyEntity $apiKey): array
    {
        $this->apiKeyResource->delete($apiKey->getId());

        return $this->apiKeyHelper->getApiKeyMessage('API key deleted - have a nice day', $apiKey);
    }
}
