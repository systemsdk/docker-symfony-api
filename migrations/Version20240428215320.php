<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

/**
 * Change api_key table
 */
final class Version20240428215320 extends AbstractMigration
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[Override]
    public function getDescription(): string
    {
        return 'Add fields: token_hash, token_parameters, change token field inside api_key table.';
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[Override]
    public function isTransactional(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE api_key ADD token_hash VARCHAR(255) DEFAULT NULL COMMENT \'Token hash (when encrypted)\' AFTER token, ADD token_parameters JSON DEFAULT NULL COMMENT \'Token decrypt parameters (when encrypted)\' AFTER token_hash, CHANGE token token VARCHAR(255) NOT NULL COMMENT \'Generated API key string for authentication\'');
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE api_key DROP token_hash, DROP token_parameters, CHANGE token token VARCHAR(40) NOT NULL COMMENT \'Generated API key string for authentication\'');
    }
}
