<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421134139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_equipement (id INT AUTO_INCREMENT NOT NULL, date_reservation DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, user_id INT NOT NULL, equipement_id INT NOT NULL, statut_id INT NOT NULL, INDEX IDX_D81E0F47A76ED395 (user_id), INDEX IDX_D81E0F47806F0F5C (equipement_id), INDEX IDX_D81E0F47F6203804 (statut_id), UNIQUE INDEX reservation_unique (user_id, equipement_id, date_reservation, heure_debut), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E564F0BFA4D60759 (libelle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement ADD CONSTRAINT FK_D81E0F47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement ADD CONSTRAINT FK_D81E0F47806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement ADD CONSTRAINT FK_D81E0F47F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement DROP FOREIGN KEY FK_D81E0F47A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement DROP FOREIGN KEY FK_D81E0F47806F0F5C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_equipement DROP FOREIGN KEY FK_D81E0F47F6203804
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE statut
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
