<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function homepage(QuoteRepository $quoteRepository): Response
    {

        $last3Quotes = $quoteRepository->findBy([], ["createdAt" => "DESC"], 3);

        return $this->render('main/homepage.html.twig', [
            'last3Quotes' => $last3Quotes,
        ]);
    }

    /**
     * @Route("/quote/{id}", name="quote_read", requirements={"id"="\d+"})
     */
    public function read(Quote $quote)
    {
        return $this->render('quote/read.html.twig', [
            'quote' => $quote,
        ]);
    }

    /**
     * @Route("/quote/add", name="quote_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $quote = new Quote();

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
}
