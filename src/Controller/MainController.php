<?php

namespace App\Controller;

use App\Entity\RoadTrip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer les 3 derniers road trips triés par ID (ou par un autre champ comme date de création si disponible)
        $roadTrips = $em->getRepository(RoadTrip::class)->findBy([], ['id' => 'DESC'], 3);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'Accueil',
            'roadTrips' => $roadTrips, // Passer les 3 derniers road trips à la vue
        ]);
    }
}
