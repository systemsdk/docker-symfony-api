<?php

declare(strict_types=1);

namespace App\Command\ApiKey;

use App\Command\Traits\SymfonyStyleTrait;
use App\Entity\ApiKey as ApiKeyEntity;
use App\Resource\ApiKeyResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class RemoveApiKeyCommand
 *
 * @package App\Command\ApiKey
 */
class RemoveApiKeyCommand extends Command
{
    use SymfonyStyleTrait;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(
        private ApiKeyResource $apiKeyResource,
        private ApiKeyHelper $apiKeyHelper,
    ) {
        parent::__construct('api-key:remove');

        $this->setDescription('Console command to remove existing API key');
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
