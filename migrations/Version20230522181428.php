<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522181428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA76ED395 ON figure (user_id)');
        $this->addSql('CREATE INDEX IDX_2F57B37A7A45358C ON figure (groupe_id)');
        $this->addSql('ALTER TABLE groupe ADD created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE image ADD created_at VARCHAR(255) NOT NULL, DROP user_id');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F5C011B5 ON image (figure_id)');
        $this->addSql('ALTER TABLE message ADD figure_id INT DEFAULT NULL, ADD user_id INT NOT NULL, CHANGE created_at created_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F5C011B5 ON message (figure_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE video DROP user_id, CHANGE created_at created_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C5C011B5 ON video (figure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F5C011B5');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('DROP INDEX IDX_B6BD307F5C011B5 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FA76ED395 ON message');
        $this->addSql('ALTER TABLE message DROP figure_id, DROP user_id, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA76ED395');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A7A45358C');
        $this->addSql('DROP INDEX IDX_2F57B37AA76ED395 ON figure');
        $this->addSql('DROP INDEX IDX_2F57B37A7A45358C ON figure');
        $this->addSql('ALTER TABLE figure DROP created_at');
        $this->addSql('ALTER TABLE groupe DROP created_at');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C5C011B5');
        $this->addSql('DROP INDEX IDX_7CC7DA2C5C011B5 ON video');
        $this->addSql('ALTER TABLE video ADD user_id INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5C011B5');
        $this->addSql('DROP INDEX IDX_C53D045F5C011B5 ON image');
        $this->addSql('ALTER TABLE image ADD user_id INT NOT NULL, DROP created_at');
    }
}
