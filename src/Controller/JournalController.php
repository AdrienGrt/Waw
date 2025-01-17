<?php
namespace App\Controller;

use App\Repository\RoadTripRepository;
use App\Entity\RoadTrip;
use App\Form\RoadTripType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; // Ajoutez cette importation

class JournalController extends AbstractController
{
    private $entityManager;

    // Injection du EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/carnet', name: 'app_journal')]
    public function journal(RoadTripRepository $roadTripRepository)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les roadtrips associés à cet utilisateur
        $roadTrips = $roadTripRepository->findBy(['user' => $user]);

        // Passer les roadtrips à la vue
        return $this->render('journal.html.twig', [
            'roadTrips' => $roadTrips
        ]);
    }

    #[Route('/roadtrip/create', name: 'app_roadtrip_create')]
    public function create(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Créer une nouvelle instance de RoadTrip
        $roadTrip = new RoadTrip();
        
        // Créer le formulaire
        $form = $this->createForm(RoadTripType::class, $roadTrip);

        // Traitement du formulaire
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté au roadtrip
            $roadTrip->setUser($this->getUser());

            // Sauvegarder dans la base de données
            $this->entityManager->persist($roadTrip);
            $this->entityManager->flush();

            // Rediriger vers la page carnet ou un autre endroit
            return $this->redirectToRoute('app_journal');
        }

        return $this->render('dashboard/roadtrip/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/roadtrip/{id}', name: 'app_dashboard_roadtrip_view')]
    public function view(RoadTrip $roadTrip)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est bien le propriétaire du roadtrip
        if ($roadTrip->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce roadtrip.');
        }

        // Passer le roadtrip à la vue
        return $this->render('roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip
        ]);
    }

    #[Route('/roadtrip/{id}/delete', name: 'app_roadtrip_delete')]
    public function delete(RoadTrip $roadTrip)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est bien le propriétaire du roadtrip
        if ($roadTrip->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce roadtrip.');
        }

        // Supprimer le roadtrip
        $this->entityManager->remove($roadTrip);
        $this->entityManager->flush();

        // Rediriger après la suppression
        return $this->redirectToRoute('app_journal');
    }
}
