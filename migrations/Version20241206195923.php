<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241206195923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent ADD supervisor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9D19E9AC5F FOREIGN KEY (supervisor_id) REFERENCES agent (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_268B9C9D19E9AC5F ON agent (supervisor_id)');
        $this->addSql('ALTER TABLE user ADD agent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8D93D6493414710B ON user (agent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9D19E9AC5F');
        $this->addSql('DROP INDEX IDX_268B9C9D19E9AC5F ON agent');
        $this->addSql('ALTER TABLE agent DROP supervisor_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493414710B');
        $this->addSql('DROP INDEX IDX_8D93D6493414710B ON user');
        $this->addSql('ALTER TABLE user DROP agent_id');
    }
}
