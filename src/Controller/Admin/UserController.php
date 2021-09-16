<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserAddType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user_browse")
     */
    public function browse(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/user/browse.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user-add", name="admin_user_add")
     */
    public function add(Request $request, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader, EntityManagerInterface $em){
        
        $user = new User();

        $form = $this->createForm(AdminUserAddType::class, $user);

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

            $this->addFlash('success', 'Vous avez bien rajouter un(e) nouvel(le) utilisateur(rice)');

            return $this->redirectToRoute('admin_user_browse');
        }

        return $this->render('admin/user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user-edit/{id}", name="admin_user_edit")
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em)
    {

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Les informations de l\'utilisateur ont bien été modifiées.');

            return $this->redirectToRoute('admin_user_browse');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user-delete/{id}", name="admin_user_delete")
     */
    public function delete(User $user, EntityManagerInterface $em)
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Les informations de l\'utilisateur ont bien été supprimée.');

        return $this->redirectToRoute('admin_user_browse');
    }
}
