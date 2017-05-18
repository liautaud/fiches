<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20161017211529 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE langages (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fiches ADD langage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiches ADD CONSTRAINT FK_459C25C9957BB53C FOREIGN KEY (langage_id) REFERENCES langages (id)');
        $this->addSql('CREATE INDEX IDX_459C25C9957BB53C ON fiches (langage_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fiches DROP FOREIGN KEY FK_459C25C9957BB53C');
        $this->addSql('DROP TABLE langages');
        $this->addSql('DROP INDEX IDX_459C25C9957BB53C ON fiches');
        $this->addSql('ALTER TABLE fiches DROP langage_id');
    }
}
