<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512131040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_espace (id INT AUTO_INCREMENT NOT NULL, statut_id INT NOT NULL, user_id INT NOT NULL, espace_travail_id INT NOT NULL, date_reservation DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, INDEX IDX_4E5A40F6F6203804 (statut_id), INDEX IDX_4E5A40F6A76ED395 (user_id), INDEX IDX_4E5A40F64A7E6B92 (espace_travail_id), UNIQUE INDEX reservation_espace_unique (user_id, espace_travail_id, date_reservation, heure_debut), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace ADD CONSTRAINT FK_4E5A40F6F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace ADD CONSTRAINT FK_4E5A40F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace ADD CONSTRAINT FK_4E5A40F64A7E6B92 FOREIGN KEY (espace_travail_id) REFERENCES espace_travail (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace DROP FOREIGN KEY FK_4E5A40F6F6203804
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace DROP FOREIGN KEY FK_4E5A40F6A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_espace DROP FOREIGN KEY FK_4E5A40F64A7E6B92
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_espace
        SQL);
    }
}
