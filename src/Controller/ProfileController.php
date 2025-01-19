<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
    public function profile(): Response
    {
        // Logique pour afficher la page Profil
        return $this->render('profile.html.twig');
    }

    #[Route('/profil/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre profil.');
        }

        // Gérer les modifications
        $username = $request->request->get('username');
        $bio = $request->request->get('bio');
        $profilePictureFile = $request->files->get('profilePicture');

        // Mettre à jour les données utilisateur
        if ($username) {
            $user->setUsername($username);
        }

        if ($bio) {
            $user->setBio($bio);
        }

        if ($profilePictureFile) {
            // Gérer le téléchargement de l'image
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
            $newFilename = uniqid() . '.' . $profilePictureFile->guessExtension();
            $profilePictureFile->move($uploadsDir, $newFilename);
            $user->setProfilePicture('uploads/images/' . $newFilename);
        }

        // Sauvegarder dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Rediriger vers la page de profil
        return $this->redirectToRoute('app_profile');
    }
}
