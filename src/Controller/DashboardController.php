<?php

namespace App\Controller;

use App\Entity\RoadTrip;
use App\Form\RoadTripType;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]  // Préfixe pour toutes les routes de ce contrôleur
class DashboardController extends AbstractController
{
    // Page principale du dashboard
    #[Route('/', name: 'app_dashboard')]  // Route pour la page principale
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    // Liste des utilisateurs
    #[Route('/users', name: 'app_dashboard_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); // Limite l'accès aux utilisateurs authentifiés

        $users = $userRepository->findAll();
        return $this->render('dashboard/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    // Modifier un utilisateur
    #[Route('/users/{id}/edit', name: 'app_dashboard_user_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editUser(
        int $id,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER'); // Limite l'accès aux utilisateurs authentifiés

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('app_dashboard_users'); // Redirection vers la liste des utilisateurs
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un utilisateur
    #[Route('/users/{id}/delete', name: 'app_dashboard_user_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Limite l'accès à l'admin

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_dashboard_users'); // Redirection vers la liste des utilisateurs
    }

    // Créer un road trip
    #[Route('/roadtrip/create', name: 'app_dashboard_roadtrip_create')]
    public function createRoadTrip(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // Limite l'accès aux utilisateurs authentifiés

        $roadTrip = new RoadTrip(); // Créer un nouvel objet RoadTrip

        $form = $this->createForm(RoadTripType::class, $roadTrip); // Créer le formulaire pour RoadTrip

        $form->handleRequest($request); // Traiter la requête

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // Récupérer l'utilisateur connecté
            $roadTrip->setUser($user); // Associer l'utilisateur au road trip

            $em->persist($roadTrip); // Persister les données en base
            $em->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'Le road trip a été créé avec succès.');

            // Rediriger vers le tableau de bord
            return $this->redirectToRoute('app_dashboard'); // Redirection vers le tableau de bord
        }

        // Afficher le formulaire
        return $this->render('dashboard/roadtrip/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
