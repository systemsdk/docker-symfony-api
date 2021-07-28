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
 * Class ChangeTokenCommand
 *
 * @package App\Command\ApiKey
 */
class ChangeTokenCommand extends Command
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
        parent::__construct('api-key:change-token');

        $this->setDescription('Command to change token for existing API key');
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
        $apiKey = $this->apiKeyHelper->getApiKey($io, 'Which API key token you want to change?');
        $message = $apiKey instanceof ApiKeyEntity ? $this->changeToken($apiKey) : null;

        if ($input->isInteractive()) {
            $io->success($message ?? ['Nothing changed - have a nice day']);
        }

        return 0;
    }

    /**
     * Method to change API key token.
     *
     * @throws Throwable
     *
     * @return array<int, string>
     */
    private function changeToken(ApiKeyEntity $apiKey): array
    {
        // Generate new token for API key
        $apiKey->generateToken();
        // Update API key
        $this->apiKeyResource->save($apiKey);

        return $this->apiKeyHelper->getApiKeyMessage('API key token updated - have a nice day', $apiKey);
    }
}
