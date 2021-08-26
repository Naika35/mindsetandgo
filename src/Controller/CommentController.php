<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/edit/{id}", name="comment_edit")
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUser($this->getUser());

            $comment->setQuote($this->get);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $comment->getQuote()->getId()]);
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
