<?php

namespace App\Controller;

use App\Entity\RoadTrip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoyageController extends AbstractController
{
    #[Route('/voyage', name: 'app_voyage')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer les RoadTrips
        $roadTrips = $em->getRepository(RoadTrip::class)->findAll();

        // Passer les RoadTrips à la vue
        return $this->render('voyage/index.html.twig', [
            'roadTrips' => $roadTrips
        ]);
    }
}




