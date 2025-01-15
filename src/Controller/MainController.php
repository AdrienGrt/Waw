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
        // Récupérer tous les road trips
        $roadTrips = $em->getRepository(RoadTrip::class)->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'Accueil',
            'roadTrips' => $roadTrips, // Passer les road trips à la vue
        ]);
    }
}
