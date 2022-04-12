<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash(
                'success',
                'Votre formulaire de contact a bien été envoyé',       
            );

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('contact', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
