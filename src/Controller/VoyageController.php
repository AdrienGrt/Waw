<?php

namespace App\Controller;

use App\Entity\RoadTrip;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoyageController extends AbstractController
{
    #[Route('/voyage', name: 'app_voyage')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $roadTripRepo = $em->getRepository(RoadTrip::class);

        // Récupération des filtres
        $selectedDestination = $request->query->get('destination');
        $selectedVehicle = $request->query->get('vehicle');

        // Construction de la requête dynamique avec des filtres
        $query = $roadTripRepo->createQueryBuilder('r')
            ->leftJoin('r.vehicle', 'v')
            ->addSelect('v')
            ->leftJoin('r.medias', 'm')
            ->addSelect('m')
            ->where('r.isPublic = true');

        if ($selectedDestination) {
            $query->andWhere('r.arrive_address = :destination') // ✅ Correction ici
                  ->setParameter('destination', $selectedDestination);
        }

        if ($selectedVehicle) {
            $query->andWhere('v.type = :vehicle')
                  ->setParameter('vehicle', $selectedVehicle);
        }

        // Exécuter la requête filtrée
        $roadTrips = $query->orderBy('r.id', 'DESC')->getQuery()->getResult();

        // Récupérer les destinations uniques
        $destinations = array_column(
            $em->createQueryBuilder()
                ->select('DISTINCT r.arrive_address') // ✅ Correction ici
                ->from(RoadTrip::class, 'r')
                ->where('r.arrive_address IS NOT NULL')
                ->getQuery()
                ->getResult(),
            'arrive_address'
        );

        // Récupérer les types de véhicules uniques
        $vehicles = array_column(
            $em->createQueryBuilder()
                ->select('DISTINCT v.type')
                ->from(Vehicle::class, 'v')
                ->getQuery()
                ->getResult(),
            'type'
        );

        return $this->render('voyage/index.html.twig', [
            'roadTrips' => $roadTrips,
            'destinations' => $destinations,
            'vehicles' => $vehicles,
            'selected_destination' => $selectedDestination,
            'selected_vehicle' => $selectedVehicle
        ]);
    }
}
