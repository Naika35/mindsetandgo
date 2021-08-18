<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/slug", name="read")
     */
    public function index(): Response
    {
        return $this->render('user/read.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em) {

        $user = new User();

        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du password
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));  

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vous êtes bien enregistrée). Vous pouvez dès à présent vous connecter.');

            return $this->redirectToRoute('main'); // mettre la page login
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
