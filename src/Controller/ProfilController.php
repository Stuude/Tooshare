<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\ProfilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'profil')]
    public function profil(): Response
    {
        return $this->render('profil/index.html.twig', [
            
        ]);
    }


    #[Route('/profil/update', name: 'profil_update')]

    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $imgFile = $form->get('profile_picture')->getData();
            //     if ($imgFile) {
            //         $destination= $this->getParameter('uploads');
            //         $newFilename = $fileUploader->upload($imgFile,$user,$destination,$slugger);
            //         if ($newFilename != null) {
            //             $user->setImage($newFilename);
            //         }
            //     }
         
            // encode the plain password
            if ($userPasswordHasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {

                $imgFile = $form->get('profile_picture')->getData();
                if ($imgFile) {
                    $destination= $this->getParameter('uploads');
                    $newFilename = $fileUploader->upload($imgFile,$user,$destination,$slugger);
                    if ($newFilename != null) {
                        $user->setImage($newFilename);
                    }
                }
                if ($form->get('newPassword')->getData()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('newPassword')->getData()
                        )
                    );
                    $this->addFlash(
                        'success',
                        'Vos changements ont bien été pris en compte',       
                    );
                }
            }
            else{
                $this->addFlash(
                    'error',
                    'Vos changements n\'ont pas été pris en compte car le mot de passe est incorrect',
    
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/update.html.twig', [
            'profilForm' => $form->createView(),

        ]);
    }
}
