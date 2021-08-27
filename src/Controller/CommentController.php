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

            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été modifié');

            return $this->redirectToRoute('quote_read', ['id' => $comment->getQuote()->getId()]);
        }
        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     */
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em)
    {
        /*$this->denyAccessUnlessGranted('COMMENT_DELETE', $comment);*/

        // On vérifie le token
        $token = $request->request->get('_token');
        
        if ($this->isCsrfTokenValid('deleteComment', $token)) {
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été supprimée.');
            return $this->redirectToRoute('user_profile');
        }

        // Si le token n'est pas valide, on lance une exception Access Denied
        throw $this->createAccessDeniedException('Le token n\'est pas valide.');

    }
}
