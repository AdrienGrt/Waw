<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116171543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modification de la colonne image_supplementaire pour accepter NULL et les tableaux JSON.';
    }

    public function up(Schema $schema): void
    {
        // Assurez-vous que la colonne image_supplementaire peut contenir NULL et des données JSON valides
        $this->addSql('ALTER TABLE road_trip CHANGE image_supplementaire image_supplementaire JSON NULL');
    }

    public function down(Schema $schema): void
    {
        // En cas de rollback, vous pouvez redéfinir la colonne à VARCHAR
        $this->addSql('ALTER TABLE road_trip CHANGE image_supplementaire image_supplementaire VARCHAR(255) DEFAULT NULL');
    }
}
