<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20161016031349 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE exercices (id INT AUTO_INCREMENT NOT NULL, fiche_id INT DEFAULT NULL, numero INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_1387EAE1DF522508 (fiche_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiches (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dependances (suivante_id INT NOT NULL, precedente_id INT NOT NULL, INDEX IDX_78BC984C4D104D (suivante_id), INDEX IDX_78BC984C562D9ABA (precedente_id), PRIMARY KEY(suivante_id, precedente_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendus (id INT AUTO_INCREMENT NOT NULL, fiche_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, etat INT NOT NULL, date_creation DATETIME NOT NULL, date_traitement DATETIME DEFAULT NULL, INDEX IDX_A999BBADDF522508 (fiche_id), INDEX IDX_A999BBADFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses (id INT AUTO_INCREMENT NOT NULL, exercice_id INT DEFAULT NULL, rendu_id INT DEFAULT NULL, numero INT NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_1E512EC689D40298 (exercice_id), INDEX IDX_1E512EC6C974D9ED (rendu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, responsable_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, statut INT NOT NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(255) DEFAULT NULL, INDEX IDX_497B315E53C59D72 (responsable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exercices ADD CONSTRAINT FK_1387EAE1DF522508 FOREIGN KEY (fiche_id) REFERENCES fiches (id)');
        $this->addSql('ALTER TABLE dependances ADD CONSTRAINT FK_78BC984C4D104D FOREIGN KEY (suivante_id) REFERENCES fiches (id)');
        $this->addSql('ALTER TABLE dependances ADD CONSTRAINT FK_78BC984C562D9ABA FOREIGN KEY (precedente_id) REFERENCES fiches (id)');
        $this->addSql('ALTER TABLE rendus ADD CONSTRAINT FK_A999BBADDF522508 FOREIGN KEY (fiche_id) REFERENCES fiches (id)');
        $this->addSql('ALTER TABLE rendus ADD CONSTRAINT FK_A999BBADFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC689D40298 FOREIGN KEY (exercice_id) REFERENCES exercices (id)');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC6C974D9ED FOREIGN KEY (rendu_id) REFERENCES rendus (id)');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315E53C59D72 FOREIGN KEY (responsable_id) REFERENCES utilisateurs (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC689D40298');
        $this->addSql('ALTER TABLE exercices DROP FOREIGN KEY FK_1387EAE1DF522508');
        $this->addSql('ALTER TABLE dependances DROP FOREIGN KEY FK_78BC984C4D104D');
        $this->addSql('ALTER TABLE dependances DROP FOREIGN KEY FK_78BC984C562D9ABA');
        $this->addSql('ALTER TABLE rendus DROP FOREIGN KEY FK_A999BBADDF522508');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC6C974D9ED');
        $this->addSql('ALTER TABLE rendus DROP FOREIGN KEY FK_A999BBADFB88E14F');
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315E53C59D72');
        $this->addSql('DROP TABLE exercices');
        $this->addSql('DROP TABLE fiches');
        $this->addSql('DROP TABLE dependances');
        $this->addSql('DROP TABLE rendus');
        $this->addSql('DROP TABLE reponses');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
