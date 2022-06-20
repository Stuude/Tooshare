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
        // on récupere le user
        $user = $this->getUser();
        // on créer le formulaire pour la modification du profil
        $form = $this->createForm(ProfilType::class, $user);
        // on gère la requete
        $form->handleRequest($request);
        // boucle if si le form est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if ($userPasswordHasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {
                // on récupere les données de l'ancienne photo de profil
                $imgFile = $form->get('profile_picture')->getData();
                if ($imgFile) {
                    $destination= $this->getParameter('uploads');
                    // variable newfilename pour la rechanger si besoin
                    $newFilename = $fileUploader->upload($imgFile,$user,$destination,$slugger);
                    if ($newFilename != null) {
                        // le setImage pour l'image
                        $user->setImage($newFilename);
                    }
                }
                // 
                if ($form->get('newPassword')->getData()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('newPassword')->getData()
                        )
                    );
                    // addflash qui nous sert a faire une intervention front sympa
                    $this->addFlash(
                        'success',
                        'Vos changements ont bien été pris en compte',       
                    );
                }
            }
            else{
                // addflash qui nous sert a faire une intervention front sympa
                $this->addFlash(
                    'error',
                    'Vos changements n\'ont pas été pris en compte car le mot de passe est incorrect',
    
                );
            }
            // entity manager qui sert a manipuler les données de la base de donnée
            $entityManager->persist($user);
            $entityManager->flush();
            // on redirige directement vers le profil
            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/update.html.twig', [
            'profilForm' => $form->createView(),

        ]);
    }
}
