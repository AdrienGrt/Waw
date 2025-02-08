<?php
namespace App\Controller;

use App\Repository\RoadTripRepository;
use App\Entity\RoadTrip;
use App\Entity\Media;
use App\Form\RoadTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/carnet')]
class JournalController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_journal')]
    public function journal(RoadTripRepository $roadTripRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $roadTrips = $roadTripRepository->findBy(['user' => $user]);

        return $this->render('journal.html.twig', [
            'roadTrips' => $roadTrips
        ]);
    }

    #[Route('/roadtrip/{id}', name: 'app_dashboard_roadtrip_view')]
    public function view(RoadTrip $roadTrip): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($roadTrip->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce roadtrip.');
        }

        return $this->render('roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dashboard_roadtrip_edit', methods: ['GET', 'POST'])]
public function editRoadTrip(int $id, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

    if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
    }

    // Récupérer le referer (l'URL de la page précédente)
    $referer = $request->headers->get('referer');

    $form = $this->createForm(RoadTripType::class, $roadTrip);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        foreach ($form->get('medias')->getData() as $mediaForm) {
            $file = $mediaForm['file']; 
        
            if ($file instanceof UploadedFile) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        
                $uploadDir = $this->getParameter('uploads_directory');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }
        
                $file->move($uploadDir, $newFilename);
        
                $media = new Media();
                $media->setFileName($newFilename);
                $media->setFilePath('/uploads/images/' . $newFilename);
                $media->setRoadTrip($roadTrip);
        
                $roadTrip->addMedia($media);
                $em->persist($media);
            }
        }

        $em->flush();
        $this->addFlash('success', 'Le road trip a été mis à jour avec succès.');

        // Rediriger vers la page précédente ou la liste des roadtrips
        return $this->redirect($referer ?: $this->generateUrl('app_journal'));
    }

    return $this->render('dashboard/roadtrip/edit.html.twig', [
        'form' => $form->createView(),
        'roadTrip' => $roadTrip,
        'referer' => $referer // Passer le referer à Twig
    ]);
}

    #[Route('/roadtrip/{id}/delete', name: 'app_dashboard_roadtrip_delete', methods: ['POST'])]
    public function delete(RoadTrip $roadTrip, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($roadTrip->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce roadtrip.');
        }

        // Vérification CSRF token
        if ($this->isCsrfTokenValid('delete' . $roadTrip->getId(), $request->request->get('_token'))) {
            $em->remove($roadTrip);
            $em->flush();
            $this->addFlash('success', 'Le road trip a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_journal');
    }
}
