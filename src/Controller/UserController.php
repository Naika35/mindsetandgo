<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use App\Form\UserEditType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
            // Hash du password
            $newPassword = $form->get('password')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));


            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarFileName = $fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vous êtes bien enregistrée). Vous pouvez dès à présent vous connecter.');

            return $this->redirectToRoute('app_login'); // mettre la page login
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user-profile/{slug}", name="user_read")
     */
    public function read(User $user)
    {

        return $this->render('user/read.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("user/profile", name="user_profile")
     */
    public function profile()
    {
        $user = $this->getUser();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("user/edit", name="user_edit")
     */
    public function edit(Request $request, FileUploader $fileUploader, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
    {

        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $form->get('password')->getData();

            if ($newPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarFileName = $fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);
            }

            $em->flush();

            $this->addFlash('success', 'Profil modifié.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user-delete/{id}", name="user_delete")
     */
    public function delete(Request $request, User $user, EntityManagerInterface $em)
    {
        /*$this->denyAccessUnlessGranted('USER_DELETE', $user);*/

        // On vérifie le token
        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('deleteUser', $token)) {

            // Supprimer la session sinon conflit avec la navbar {% is_granted('IS_AUTHENTICATE_FULLY') %}
            $session = new Session();
            $session->invalidate(); // Efface tous les attributs de session et flashes et régénère la session et supprime l'ancienne session de la persistance.
            
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Le profile a bien été supprimée. A bientôt');
            return $this->redirectToRoute('homepage');
        }

        // Si le token n'est pas valide, on lance une exception Access Denied
        throw $this->createAccessDeniedException('Le token n\'est pas valide.');
    }
}
