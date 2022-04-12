<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArtisteRepository;
use App\Entity\Artiste;
use App\Entity\Comment;
use App\Form\CommentType;


#[Route('/artiste')]
class ArtisteController extends AbstractController
{
    #[Route('/', name: 'artiste')]
    public function index(ArtisteRepository $artisteRepository): Response

    {
        return $this->render('artiste/index.html.twig', [
            'controller_name' => 'ArtisteController',
            'artistes' => $artisteRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'artiste_show', methods: ['GET'])]

    public function show(Artiste $artiste): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('artiste/show.html.twig', [
            'artiste' => $artiste,
            'form' => $form->createView(),
        ]);
    }
    
}
