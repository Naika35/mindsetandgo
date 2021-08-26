<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Quote;
use App\Form\CommentType;
use App\Form\QuoteType;
use App\Repository\CategoryRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @Route("/quote/{id}", name="quote_read", requirements={"id"="\d+"})
     */
    public function read(EntityManagerInterface $em, Request $request, Quote $quote)
    {
        if(!$quote){
            throw new NotFoundHttpException('Cette citation n\'existe pas');
        }

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUser($this->getUser());

            $comment->setQuote($quote);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $comment->getQuote()->getId()]);
        }
        
        return $this->render('quote/read.html.twig', [
            'quote' => $quote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quote/category/{slug}", name="quote_by_category" )
     */
    public function quoteByCategory(Category $category){

        
        return $this->render('quote/byCategory.html.twig', [
            'category' => $category,
            ]);
    }



    /**
     * @Route("/quote/add", name="quote_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        /* $this->denyAccessUnlessGranted('QUOTE_EDIT', $quote); */

        $quote = new Quote();

        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Association de la citation au user connecté
            $quote->setUser($this->getUser());

            /* $categories = $form->get('categories')->getData();
            dd($categories);
            $quote->addCategory($categories); */

            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $quote->getId()]);
        }

        return $this->render('quote/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quote/edit/{id}", name="quote_edit", requirements={"id"="\d+"})
     */
    public function edit(Quote $quote, Request $request, EntityManagerInterface $em)
    {
        /* $this->denyAccessUnlessGranted('QUOTE_EDIT', $quote); */

        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'La citation a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $quote->getId()]);
        }

        return $this->render('quote/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("quote/delete/{id}", name="quote_delete")
     */
    public function delete(Request $request, Quote $quote, EntityManagerInterface $em)
    {
        /*$this->denyAccessUnlessGranted('QUOTE_DELETE', $movie);*/

        // On vérifie le token
        $token = $request->request->get('_token');
        
        if ($this->isCsrfTokenValid('deleteQuote', $token)) {
            $em->remove($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été supprimée.');
            return $this->redirectToRoute('user_profile');
        }

        // Si le token n'est pas valide, on lance une exception Access Denied
        throw $this->createAccessDeniedException('Le token n\'est pas valide.');
       
    }
}
