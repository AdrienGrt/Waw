<?php

namespace App\Controller;

use App\Entity\RoadTrip;
use App\Form\RoadTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
