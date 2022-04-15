<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArtisteRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArtisteRepository $artisteRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'controller_name' => 'HomeController',

            /* On vient chercher la fonction findXlast qu'on a créer dans
               ArtisteRepository et on l'appelle pour pouvoir afficher les
               trois derniers articles/artistes */
            'artistes' => $artisteRepository->findXLast(3),
        ]);
    }
}
