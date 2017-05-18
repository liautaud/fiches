<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20161018234002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exercices DROP FOREIGN KEY FK_1387EAE1DF522508');
        $this->addSql('ALTER TABLE exercices ADD CONSTRAINT FK_1387EAE1DF522508 FOREIGN KEY (fiche_id) REFERENCES fiches (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC689D40298');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC689D40298 FOREIGN KEY (exercice_id) REFERENCES exercices (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exercices DROP FOREIGN KEY FK_1387EAE1DF522508');
        $this->addSql('ALTER TABLE exercices ADD CONSTRAINT FK_1387EAE1DF522508 FOREIGN KEY (fiche_id) REFERENCES fiches (id)');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC689D40298');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC689D40298 FOREIGN KEY (exercice_id) REFERENCES exercices (id)');
    }
}
