<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VehicleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des types de véhicules à insérer
        $types = ['voiture', 'van', 'moto', 'avion'];

        foreach ($types as $type) {
            $vehicle = new Vehicle();
            $vehicle->setType($type);
            $manager->persist($vehicle); // Préparation pour l'insertion
        }

        $manager->flush(); // Exécution de l'insertion
    }
}
