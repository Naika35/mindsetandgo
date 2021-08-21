<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
      /**
     * @Route("/admin/comment", name="admin_comment_browse")
     */
    public function browse(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/comment/browse.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/admin/comment-add", name="admin_comment_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été rajoutée');

            return $this->redirectToRoute('admin_comment_browse');
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/comment-edit/{id}", name="admin_comment_edit")
     */
    public function edit(Request $request, Comment $comment, EntityManagerInterface $em)
    {
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été modifiée');

            return $this->redirectToRoute('admin_comment_browse');
        }
        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/comment-delete/{id}", name="admin_comment_delete")
     */
    public function delete(Comment $comment, EntityManagerInterface $em)
    {
        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', 'La citation a bien été supprimée.');

        return $this->redirectToRoute('admin_comment_browse');
    }
}
