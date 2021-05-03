<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503154652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_23A0E66FF7747B4D0A91634B86340489C2003F ON article');
        $this->addSql('CREATE FULLTEXT INDEX IDX_23A0E66FF7747B489C2003F ON article (titre, contenu)');
        $this->addSql('ALTER TABLE media CHANGE type type VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_23A0E66FF7747B489C2003F ON article');
        $this->addSql('CREATE FULLTEXT INDEX IDX_23A0E66FF7747B4D0A91634B86340489C2003F ON article (titre, legende, sommaire, contenu)');
        $this->addSql('ALTER TABLE media CHANGE type type VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
