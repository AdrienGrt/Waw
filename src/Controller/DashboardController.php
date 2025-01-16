<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RoadTrip;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')] // Préfixe pour toutes les routes de ce contrôleur
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/users', name: 'app_dashboard_users', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $users = $userRepository->findAll();
        return $this->render('dashboard/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dashboard_roadtrip_delete', methods: ['POST'])]
public function deleteRoadTrip(int $id, EntityManagerInterface $em): Response
{
    $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

    if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
    }

    $em->remove($roadTrip);
    $em->flush();

    $this->addFlash('success', 'Road trip supprimé avec succès.');

    return $this->redirectToRoute('app_dashboard_roadtrip_list');
}

}
