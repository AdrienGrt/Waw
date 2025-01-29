<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\RoadTrip;
use App\Form\RoadTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/roadtrip')]
class RoadTripController extends AbstractController
{
    #[Route('/create', name: 'app_dashboard_roadtrip_create', methods: ['GET', 'POST'])]
    public function createRoadTrip(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $roadTrip = new RoadTrip();
        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('medias') as $mediaForm) {
                $file = $mediaForm->get('file')->getData(); // ✅ Récupère le fichier
        
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
        
            $roadTrip->setUser($this->getUser());
            $em->persist($roadTrip);
            $em->flush();
        
            $this->addFlash('success', 'Le road trip a été créé avec succès.');
            return $this->redirectToRoute('app_dashboard_roadtrip_list');
        }
        return $this->render('dashboard/roadtrip/create.html.twig', [
            'form' => $form->createView(),
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

    

    #[Route('/{id}/edit', name: 'app_dashboard_roadtrip_edit', methods: ['GET', 'POST'])]
    public function editRoadTrip(int $id, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('medias')->getData() as $mediaForm) {
                $file = $mediaForm['file']; // Récupère le fichier
        
                if ($file && $file instanceof UploadedFile) {
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
        }
        

        return $this->render('dashboard/roadtrip/edit.html.twig', [
            'form' => $form->createView(),
            'roadTrip' => $roadTrip,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dashboard_roadtrip_delete', methods: ['POST'])]
    public function deleteRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);

        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $roadTrip->getId(), $token)) {
            throw new \Exception('Token CSRF invalide.');
        }

        // Suppression des fichiers associés
        foreach ($roadTrip->getMedias() as $media) {
            $filePath = $this->getParameter('uploads_directory') . '/' . $media->getFileName();
            if (file_exists($filePath)) {
                unlink($filePath); // Supprime le fichier
            }
            $em->remove($media);
        }

        $em->remove($roadTrip);
        $em->flush();

        $this->addFlash('success', 'Le road trip a été supprimé avec succès.');
        return $this->redirectToRoute('app_dashboard_roadtrip_list');
    }


    #[Route('/{id}', name: 'app_dashboard_roadtrip_view', methods: ['GET'])]
    public function viewRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);
    
        if (!$roadTrip) {
            throw $this->createNotFoundException('Road trip introuvable.');
        }
    
        // Récupérer l'URL précédente (page de laquelle l'utilisateur vient)
        $referer = $request->headers->get('referer', $this->generateUrl('app_dashboard_roadtrip_list'));
    
        return $this->render('dashboard/roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip,
            'referer' => $referer, // ✅ On passe bien `referer` à Twig
        ]);
    }
    
}
