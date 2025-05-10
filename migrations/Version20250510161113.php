<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510161113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE bureau (id INT NOT NULL, type_bureau_id INT NOT NULL, nombre_poste INT NOT NULL, disponible_en_permanent TINYINT(1) NOT NULL, INDEX IDX_166FDEC486972EF2 (type_bureau_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE espace_collaboration (id INT NOT NULL, type_ambiance_id INT NOT NULL, mobilier_modulable TINYINT(1) NOT NULL, zone_cafe_proche TINYINT(1) NOT NULL, INDEX IDX_28098CC27CB98D02 (type_ambiance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE espace_travail (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, capacite INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_equipement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, equipement_id INT NOT NULL, statut_id INT NOT NULL, date_reservation DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, INDEX IDX_D81E0F47A76ED395 (user_id), INDEX IDX_D81E0F47806F0F5C (equipement_id), INDEX IDX_D81E0F47F6203804 (statut_id), UNIQUE INDEX reservation_unique (user_id, equipement_id, date_reservation, heure_debut), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle_reunion (id INT NOT NULL, equipement_visio_conference TINYINT(1) NOT NULL, reservation_obligatoire TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E564F0BFA4D60759 (libelle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_ambiance (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_bureau (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bureau ADD CONSTRAINT FK_166FDEC486972EF2 FOREIGN KEY (type_bureau_id) REFERENCES type_bureau (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bureau ADD CONSTRAINT FK_166FDEC4BF396750 FOREIGN KEY (id) REFERENCES espace_travail (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE espace_collaboration ADD CONSTRAINT FK_28098CC27CB98D02 FOREIGN KEY (type_ambiance_id) REFERENCES type_ambiance (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE espace_collaboration ADD CONSTRAINT FK_28098CC2BF396750 FOREIGN KEY (id) REFERENCES espace_travail (id) ON DELETE CASCADE
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
        $this->addSql(<<<'SQL'
            ALTER TABLE salle_reunion ADD CONSTRAINT FK_7636C8D3BF396750 FOREIGN KEY (id) REFERENCES espace_travail (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE bureau DROP FOREIGN KEY FK_166FDEC486972EF2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bureau DROP FOREIGN KEY FK_166FDEC4BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE espace_collaboration DROP FOREIGN KEY FK_28098CC27CB98D02
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE espace_collaboration DROP FOREIGN KEY FK_28098CC2BF396750
        SQL);
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
            ALTER TABLE salle_reunion DROP FOREIGN KEY FK_7636C8D3BF396750
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bureau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE espace_collaboration
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE espace_travail
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle_reunion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE statut
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_ambiance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_bureau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
