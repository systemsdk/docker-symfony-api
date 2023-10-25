<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20231029164236
 */
final class Version20231029164236 extends AbstractMigration
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDescription(): string
    {
        return 'Change headers, parameters fields inside log_request table';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $sql = <<<SQL
ALTER TABLE log_request
CHANGE headers headers JSON NOT NULL COMMENT '(DC2Type:json)',
CHANGE parameters parameters JSON NOT NULL COMMENT '(DC2Type:json)'
SQL;

        $this->addSql($sql);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $sql = <<<SQL
ALTER TABLE log_request
CHANGE headers headers LONGTEXT NOT NULL COMMENT '(DC2Type:json)',
CHANGE parameters parameters LONGTEXT NOT NULL COMMENT '(DC2Type:json)'
SQL;

        $this->addSql($sql);
    }
}
