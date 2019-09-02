<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190816101057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, partenaire_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, compte VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649450FF010 (telephone), INDEX IDX_8D93D64998DE13AC (partenaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, partenaires_id INT NOT NULL, numerocompte VARCHAR(255) NOT NULL, solde DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_CFF65260EA9195C1 (numerocompte), INDEX IDX_CFF6526038898CF5 (partenaires_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, bonrneinferieure INT NOT NULL, bornesuperieure INT NOT NULL, valeur INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, datedepot DATETIME NOT NULL, INDEX IDX_47948BBCF2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, ninea VARCHAR(255) NOT NULL, raionsociale VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_32FFA373C678AEBE (ninea), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, prix_id INT NOT NULL, user_id INT DEFAULT NULL, nom_envoyeur VARCHAR(255) NOT NULL, nom_receveur VARCHAR(255) NOT NULL, cni_envoyeur VARCHAR(255) NOT NULL, telephone_envoyeur VARCHAR(255) NOT NULL, telephone_receveur VARCHAR(255) NOT NULL, code_envoi VARCHAR(255) NOT NULL, montant_envoi INT NOT NULL, commission_ttc VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_723705D1944722F2 (prix_id), INDEX IDX_723705D1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64998DE13AC FOREIGN KEY (partenaire_id) REFERENCES partenaire (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526038898CF5 FOREIGN KEY (partenaires_id) REFERENCES partenaire (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1944722F2 FOREIGN KEY (prix_id) REFERENCES tarif (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCF2C56620');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1944722F2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64998DE13AC');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526038898CF5');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE transaction');
    }
}
