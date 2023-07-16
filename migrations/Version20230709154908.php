<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230709154908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE groupe CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE image CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE video CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video CHANGE created_at created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE image CHANGE created_at created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE figure CHANGE created_at created_at VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE groupe CHANGE created_at created_at VARCHAR(50) NOT NULL');
    }
}
