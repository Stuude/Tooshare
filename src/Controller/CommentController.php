<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{

    #[Route('/new/{id}', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Artiste $artiste): Response
    {

        // NOUVEL OBJET COMMENTAIRE
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // addflash qui nous sert a faire une intervention front sympa
            $this->addFlash(
                'success',
                'Votre commentaire a bien été ajouté',       
            );
            // on récupere les données du formulaire commentaire
            $comment = $form->getData();
            // on ajoute une méthode setCreateAt pour avoir la date du commentaire
            $comment->setCreatedAt(new DateTime());
            // ON récuprere le pseudo du user 
            $comment->setPseudo($this->getUser());

            $comment->setArtiste($artiste);

            // entity manager qui sert a manipuler les données de la base de données
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('artiste_show', ["id"=>$artiste->getId()], Response::HTTP_SEE_OTHER);
        }

        
    }

    #[Route('/{id}', name: 'comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // on recréer le form pour l'edit du commentaire
        $form = $this->createForm(CommentType::class, $comment);
        // on gere la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            // addflash qui nous sert a faire une intervention front sympa
            $this->addFlash(
                'success',
                'Vos changements ont bien été pris en compte',       
            );

            return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            // addflash qui nous sert a faire une intervention front sympa
            $this->addFlash(
                'success',
                'Votre commentaire a bien été supprimé',       
            );
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
    }
}
