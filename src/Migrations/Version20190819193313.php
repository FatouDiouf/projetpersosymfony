<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819193313 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction ADD commission_etat DOUBLE PRECISION DEFAULT NULL, ADD commision_envoi DOUBLE PRECISION DEFAULT NULL, ADD commission_retrait DOUBLE PRECISION DEFAULT NULL, ADD commission_system DOUBLE PRECISION DEFAULT NULL, ADD date_envoi DATETIME DEFAULT NULL, ADD date_retrait DATETIME DEFAULT NULL, ADD total DOUBLE PRECISION NOT NULL, ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction DROP commission_etat, DROP commision_envoi, DROP commission_retrait, DROP commission_system, DROP date_envoi, DROP date_retrait, DROP total, DROP type');
    }
}
