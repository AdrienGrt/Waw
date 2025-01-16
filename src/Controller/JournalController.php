<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JournalController extends AbstractController
{
    #[Route('/carnet', name: 'app_journal')]
    public function journal()
    {
        // Logique pour afficher la page Carnet
        return $this->render('journal.html.twig');
    }
}
