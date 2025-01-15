<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250115145903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les types de véhicules dans la table Vehicle.';
    }

    public function up(Schema $schema): void
    {
        // Ajouter les types de véhicules
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Moto')");
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Voiture')");
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Van')");
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Train')");
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Avion')");
        $this->addSql("INSERT INTO vehicle (type) VALUES ('Vélo')");

        // Si vous avez besoin de modifier d'autres colonnes, gardez vos instructions SQL existantes
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les types de véhicules
        $this->addSql("DELETE FROM vehicle WHERE type IN ('Moto', 'Voiture', 'Van', 'Train', 'Avion', 'Vélo')");

        // Revenir aux colonnes initiales
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
