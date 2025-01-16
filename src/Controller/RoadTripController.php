<?php
namespace App\Controller;

use App\Entity\RoadTrip;
use App\Form\RoadTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/roadtrip')]
class RoadTripController extends AbstractController
{
    #[Route('/create', name: 'app_dashboard_roadtrip_create', methods: ['GET', 'POST'])]
    public function createRoadTrip(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $roadTrip = new RoadTrip();
        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'image principale
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('uploads_directory'), $imageFileName);
                $roadTrip->setImage($imageFileName);
            }

            // Gérer les images supplémentaires
$extraImages = $form->get('image_supplementaire')->getData();
if ($extraImages) {
    $filenames = [];
    foreach ($extraImages as $extraImage) {
        $extraImageName = uniqid() . '.' . $extraImage->guessExtension();
        $extraImage->move($this->getParameter('uploads_directory'), $extraImageName);
        $filenames[] = $extraImageName;
    }
    // On s'assure que c'est toujours un tableau (même si vide)
    $roadTrip->setImageSupplementaire($filenames);
} else {
    // Si aucune image supplémentaire n'est ajoutée, on passe un tableau vide
    $roadTrip->setImageSupplementaire([]);
}


            // Associer l'utilisateur au road trip
            $roadTrip->setUser($this->getUser());

            // Sauvegarder dans la base de données
            $em->persist($roadTrip);
            $em->flush();

            $this->addFlash('success', 'Le road trip a été créé avec succès.');
            return $this->redirectToRoute('app_dashboard_roadtrip_list');
        }

        return $this->render('dashboard/roadtrip/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dashboard_roadtrip_edit', methods: ['GET', 'POST'])]
    public function editRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Road trip mis à jour avec succès.');
            return $this->redirectToRoute('app_dashboard_roadtrip_list');
        }

        return $this->render('dashboard/roadtrip/edit.html.twig', [
            'form' => $form->createView(),
            'roadTrip' => $roadTrip,
        ]);
    }

    #[Route('/roadtrips', name: 'app_dashboard_roadtrip_list', methods: ['GET'])]
    public function listRoadTrips(EntityManagerInterface $em): Response
    {
        $roadTrips = $em->getRepository(RoadTrip::class)->findAll();

        return $this->render('dashboard/roadtrip/list.html.twig', [
            'roadTrips' => $roadTrips,
        ]);
    }

    #[Route('/{id}', name: 'app_dashboard_roadtrip_view', methods: ['GET'])]
    public function viewRoadTrip(int $id, EntityManagerInterface $em): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip) {
            throw $this->createNotFoundException('Road trip introuvable.');
        }

        return $this->render('dashboard/roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dashboard_roadtrip_delete', methods: ['POST'])]
    public function deleteRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        // Récupérer le road trip par son ID
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            // Vérifier si l'utilisateur est bien le propriétaire du roadtrip
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        // Vérifier le token CSRF pour s'assurer que la demande vient bien du formulaire
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $roadTrip->getId(), $token)) {
            throw new \Exception('Token CSRF invalide.');
        }

        // Supprimer le road trip de la base de données
        $em->remove($roadTrip);
        $em->flush();

        // Ajouter un message flash pour indiquer que la suppression a réussi
        $this->addFlash('success', 'Le road trip a été supprimé avec succès.');

        // Rediriger l'utilisateur vers la liste des roadtrips
        return $this->redirectToRoute('app_dashboard_roadtrip_list');
    }
}
