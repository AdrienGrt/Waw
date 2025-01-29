<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120105658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add media table and update road_trip table structure';
    }

    public function up(Schema $schema): void
    {
        // Création de la table "media"
        $this->addSql('CREATE TABLE media (
            id INT AUTO_INCREMENT NOT NULL, 
            file_name VARCHAR(255) DEFAULT NULL, 
            updated_at DATETIME DEFAULT NULL, 
            road_trip_id INT NOT NULL, 
            INDEX IDX_6A2CA10CFBF41406 (road_trip_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');

        // Ajout d'une clé étrangère vers "road_trip"
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CFBF41406 FOREIGN KEY (road_trip_id) REFERENCES road_trip (id)');

        // Vérification avant suppression des colonnes "image_file" et "updated_at" si elles existent
        $this->addSql("
            DO
            BEGIN
                IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_NAME = 'road_trip' 
                           AND COLUMN_NAME = 'image_file') THEN
                    ALTER TABLE road_trip DROP COLUMN image_file;
                END IF;
                IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_NAME = 'road_trip' 
                           AND COLUMN_NAME = 'updated_at') THEN
                    ALTER TABLE road_trip DROP COLUMN updated_at;
                END IF;
            END;
        ");

        // Modification de la table "user" pour aligner les colonnes
        $this->addSql('ALTER TABLE user CHANGE bio bio LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la table "media"
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CFBF41406');
        $this->addSql('DROP TABLE media');

        // Suppression des colonnes ajoutées/modifiées
        $this->addSql('ALTER TABLE user CHANGE bio bio TEXT DEFAULT NULL');

        // Réintégration des colonnes supprimées
        $this->addSql('ALTER TABLE road_trip ADD image_file VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }
}
