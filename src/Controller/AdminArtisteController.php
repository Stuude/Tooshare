<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Form\Artiste1Type;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;


#[Route('/admin/artiste')]
#[IsGranted('ROLE_ADMIN')]
class AdminArtisteController extends AbstractController
{
    #[Route('/', name: 'admin_artiste_index', methods: ['GET'])]
    public function index(ArtisteRepository $artisteRepository): Response
    {
        return $this->render('admin_artiste/index.html.twig', [
            // Le findAll qui est une fonction du reponsitory qui nous sert a récupérer tous les artistes
            'artistes' => $artisteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_artiste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        // on instancie une classe qui devient donc un objet et on créer un nouvel artiste
        $artiste = new Artiste();
        // On créer l'artiste
        $form = $this->createForm(Artiste1Type::class, $artiste);
        // on gère la requete
        $form->handleRequest($request);

        // boucle IF si le form est rempli 
        if ($form->isSubmitted() && $form->isValid()) {
            // on récuprere l'image ajouté dans le formulaire
            $imgFile = $form->get('image')->getData();
            // Boucle if de l'image 
            if($imgFile) {
                // le getParameter est une méthode de controlleur
                $destination= $this->getParameter('uploads');
                // on fait appel au service FileUploader et on rempli tous les paramètres
                $fileUploader->upload($imgFile,$artiste,$destination,$slugger);

            }

            // addflash qui nous sert a faire une intervention front sympa
            $this->addFlash(
                'success',
                'Votre article a bien été ajouté',       
            );
            // entity manager qui sert a manipuler les données de la base de données
            $entityManager->persist($artiste);
            $entityManager->flush();

            return $this->redirectToRoute('admin_artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_artiste/new.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_artiste_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]

    public function show(Artiste $artiste): Response
    {
        return $this->render('admin_artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_artiste_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]

    public function edit(Request $request, Artiste $artiste, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {

        // On recréer le l'artiste mais cette fois pour le modifier
        $form = $this->createForm(Artiste1Type::class, $artiste);
          // on gère la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récuprere l'image ajouté dans le formulaire
            $imgFile = $form->get('image')->getData();
            if($imgFile) {
                // le getParameter est une méthode de controlleur
                $destination= $this->getParameter('uploads');
                // on fait appel au service FileUploader et on rempli tous les paramètres
                $fileUploader->upload($imgFile,$artiste,$destination,$slugger);

            }
            // addflash qui nous sert a faire une intervention front sympa
            $this->addFlash(
                'success',
                'L\'article a bien été modifié',       
            );
             // entity manager qui sert a manipuler les données de la base de données
            $entityManager->persist($artiste);
            $entityManager->flush();

            return $this->redirectToRoute('admin_artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_artiste/edit.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_artiste_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]

    public function delete(Request $request, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {

        // boucle if pour supprimer l'artiste
        if ($this->isCsrfTokenValid('delete'.$artiste->getId(), $request->request->get('_token'))) {
             // entity manager qui sert a manipuler les données de la base de données
            $entityManager->remove($artiste);
            $entityManager->flush();
        }
        $this->addFlash(
            'success',
            'L\'article a bien été supprimé',       
        );

        return $this->redirectToRoute('admin_artiste_index', [], Response::HTTP_SEE_OTHER);
    }
}
