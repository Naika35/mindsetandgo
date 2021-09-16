<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Quote;
use App\Form\CommentType;
use App\Form\QuoteType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /** Read single quote
     * @Route("/quote/{id}", name="quote_read", requirements={"id"="\d+"})
     */
    public function read(EntityManagerInterface $em, Request $request, Quote $quote, CommentRepository $commentRepository)
    {
        // if quote doesn't existe, we redirect to the page HttpException while waiting to create a custom page
        if(!$quote){
            throw new NotFoundHttpException('Cette citation n\'existe pas');
        }

        // to view quote comments
        $quoteComments = $commentRepository->findBy(['quote' => $quote], ['createdAt' => 'DESC']);
        
        // if we add new comment 
        $comment = new Comment();

        // create form
        $form = $this->createForm(CommentType::class, $comment);

        
        $form->handleRequest($request);

        // If comment sudmitted, add comment to DataBase and redirect to the template of the quote
        if ($form->isSubmitted() && $form->isValid()) {

            // Insert user connected to the new comment
            $comment->setUser($this->getUser());

            $comment->setQuote($quote);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $comment->getQuote()->getId()]);
        }
        
        // Send informations to the template
        return $this->render('quote/read.html.twig', [
            'quote' => $quote,
            'quoteComments' => $quoteComments,
            'form' => $form->createView(),
        ]);
    }

    /** Sort quote by category with category's slug
     * @Route("/quote/category/{slug}", name="quote_by_category" )
     */
    public function quoteByCategory(Category $category){

        
        return $this->render('quote/byCategory.html.twig', [
            'category' => $category,
            ]);
    }



    /** Add a new comment
     * @Route("/quote/add", name="quote_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $quote = new Quote();

        // protect the road with a vote
        $this->denyAccessUnlessGranted('QUOTE_ADD', $quote);

        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Association de la citation au user connecté
            $quote->setUser($this->getUser());

            
            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été rajoutée');

            return $this->redirectToRoute('quote_read', ['id' => $quote->getId()]);
        }

        return $this->render('quote/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /** Edit the Quote
     * @Route("/quote/edit/{id}", name="quote_edit", requirements={"id"="\d+"})
     */
    public function edit(Quote $quote, Request $request, EntityManagerInterface $em)
    {
        // protect the road with a vote
        $this->denyAccessUnlessGranted('QUOTE_EDIT', $quote); 

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
        //protect the road with a vote
        $this->denyAccessUnlessGranted('QUOTE_DELETE', $quote);

        // Check token
        $token = $request->request->get('_token');
        
        if ($this->isCsrfTokenValid('deleteQuote', $token)) {
            $em->remove($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été supprimée.');
            return $this->redirectToRoute('user_profile');
        }

        // If token doesn't valid, we redirect to the page HttpException while waiting to create a custom page
        throw $this->createAccessDeniedException('Le token n\'est pas valide.');
       
    }
}
