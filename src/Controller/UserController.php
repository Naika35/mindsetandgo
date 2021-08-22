<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use App\Repository\QuoteRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    
    /**
     * @Route("/signup", name="signup")
     */
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, FileUploader $fileUploader)
    {

        $user = new User();

        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du password
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarFileName = $fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vous êtes bien enregistrée). Vous pouvez dès à présent vous connecter.');

            return $this->redirectToRoute('main'); // mettre la page login
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/{slug}", name="user_read")
     */
    public function read(User $user){

        return $this->render('user/read.html.twig',[
            'user' => $user,
            
        ]);

    }

}
