<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $em->flush;

        $this->addFlash('success', 'Les informations de l\'utilisateur ont bien été supprimée.');

        return $this->redirectToRoute('admin_user_browse');
    }
}
