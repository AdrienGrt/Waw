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
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/roadtrip')] // Préfixe pour toutes les routes de ce contrôleur
class RoadTripController extends AbstractController
{
    #[Route('/', name: 'app_roadtrip_controller', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('roadtrip_controller/index.html.twig', [
            'controller_name' => 'RoadTripController',
        ]);
    }

    #[Route('/roadtrip/{id}/edit', name: 'app_dashboard_roadtrip_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editRoadTrip(int $id, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('roadtrip_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }

                $roadTrip->setImageFilename($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Road trip mis à jour avec succès.');
            return $this->redirectToRoute('app_dashboard_roadtrip_list');
        }

        // Utiliser `dump()` pour déboguer la variable roadTrip (à supprimer une fois l'erreur résolue)
        dump($roadTrip);

        return $this->render('roadtrip_controller/index.html.twig', [
            'form' => $form->createView(),
            'roadTrip' => $roadTrip, // Passer la variable roadTrip au template
        ]);
    }

    // Supprimer un road trip
    #[Route('/roadtrip/{id}/delete', name: 'app_dashboard_roadtrip_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
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

    // Créer un road trip
    #[Route('/roadtrip/create', name: 'app_dashboard_roadtrip_create', methods: ['GET', 'POST'])]
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

            $this->addFlash('success', 'Le road trip a été créé avec succès.');

            return $this->redirectToRoute('app_dashboard_roadtrip_list'); // Redirection vers la liste des road trips
        }

        return $this->render('dashboard/roadtrip/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/roadtrip/{id}', name: 'app_dashboard_roadtrip_view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function viewRoadTrip(int $id, EntityManagerInterface $em): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        return $this->render('dashboard/roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip,
        ]);
    }
}
