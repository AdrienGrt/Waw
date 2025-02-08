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
            $roadTrip->setUser($this->getUser());
            $roadTrip->setIsPublic($form->get('isPublic')->getData() ?? false);
        
            // Traitement de l'image principale
            $mainImageFile = $form->get('imagePrincipale')->getData();
            if ($mainImageFile) {
                $newFilename = uniqid() . '.' . $mainImageFile->guessExtension();
                $mainImageFile->move($this->getParameter('uploads_directory'), $newFilename);
                
                $media = new Media();
                $media->setFileName($newFilename);
                $media->setFilePath('/uploads/images/' . $newFilename);
                $media->setRoadTrip($roadTrip);
                
                $roadTrip->addMedia($media);
                $em->persist($media);
            }
        
            // Traitement des images supplémentaires
            $extraImages = $form->get('extraImages')->getData();
            if ($extraImages) {
                foreach ($extraImages as $file) {
                    if ($file instanceof UploadedFile) {
                        $newFilename = uniqid() . '.' . $file->guessExtension();
                        $file->move($this->getParameter('uploads_directory'), $newFilename);
        
                        $media = new Media();
                        $media->setFileName($newFilename);
                        $media->setFilePath('/uploads/images/' . $newFilename);
                        $media->setRoadTrip($roadTrip);
                        
                        $roadTrip->addMedia($media);
                        $em->persist($media);
                    }
                }
            }
        
            $em->persist($roadTrip);
            $em->flush();
        
            $this->addFlash('success', 'Le road trip a été créé avec succès.');
            return $this->redirectToRoute('app_journal'); // ✅ Redirection vers le journal
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

        $referer = $request->headers->get('referer');
        $form = $this->createForm(RoadTripType::class, $roadTrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image principale
            $mainImageFile = $form->get('imagePrincipale')->getData();
            if ($mainImageFile instanceof UploadedFile) {
                $safeFilename = $slugger->slug(pathinfo($mainImageFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainImageFile->guessExtension();

                $uploadDir = $this->getParameter('uploads_directory');
                $mainImageFile->move($uploadDir, $newFilename);

                $media = new Media();
                $media->setFileName($newFilename);
                $media->setFilePath('/uploads/images/' . $newFilename);
                $media->setRoadTrip($roadTrip);

                $roadTrip->addMedia($media);
                $em->persist($media);
            }

            // Gestion des images supplémentaires
            $extraImages = $form->get('medias')->getData();
            if ($extraImages) {
                foreach ($extraImages as $file) {
                    if ($file instanceof UploadedFile) {
                        $safeFilename = $slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                        $file->move($uploadDir, $newFilename);

                        $extraMedia = new Media();
                        $extraMedia->setFileName($newFilename);
                        $extraMedia->setFilePath('/uploads/images/' . $newFilename);
                        $extraMedia->setRoadTrip($roadTrip);

                        $roadTrip->addMedia($extraMedia);
                        $em->persist($extraMedia);
                    }
                }
            }

            $em->flush();
            return $this->redirectToRoute('app_journal');
        }

        return $this->render('dashboard/roadtrip/edit.html.twig', [
            'form' => $form->createView(),
            'roadTrip' => $roadTrip,
            'referer' => $referer,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dashboard_roadtrip_delete', methods: ['POST'])]
    public function deleteRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);
        if (!$roadTrip || $roadTrip->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Road trip introuvable ou non autorisé.');
        }

        if (!$this->isCsrfTokenValid('delete' . $roadTrip->getId(), $request->request->get('_token'))) {
            throw new \Exception('Token CSRF invalide.');
        }

        foreach ($roadTrip->getMedias() as $media) {
            $filePath = $this->getParameter('uploads_directory') . '/' . $media->getFileName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $em->remove($media);
        }

        $em->remove($roadTrip);
        $em->flush();

        $this->addFlash('success', 'Le road trip et ses médias ont été supprimés avec succès.');
        return $this->redirectToRoute('app_journal');
    }

    #[Route('/{id}', name: 'app_dashboard_roadtrip_view', methods: ['GET'])]
    public function viewRoadTrip(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $roadTrip = $em->getRepository(RoadTrip::class)->find($id);
        if (!$roadTrip) {
            throw $this->createNotFoundException('Road trip introuvable.');
        }

        $referer = $request->headers->get('referer', $this->generateUrl('app_dashboard_roadtrip_list'));
        return $this->render('dashboard/roadtrip/view.html.twig', [
            'roadTrip' => $roadTrip,
            'referer' => $referer,
        ]);
    }
}
