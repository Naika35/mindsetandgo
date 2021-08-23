<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @Route("", name="last3Quotes")
     */
    public function last3Quote(QuoteRepository $quoteRepository): Response
    {

        $last3Quotes = $quoteRepository->findBy([], ["createdAt" => "DESC"], 3);

        return $this->render('main/homepage.html.twig', [
            'last3Quotes' => $last3Quotes,
        ]);
    }

    /**
     * @Route("/quote/{id}"), name="quote_read", requirements={"id"="\d+"}
     */
    public function read(Quote $quote)
    {
        return $this->render('quote/read.html.twig', [
            'quote' => $quote,
        ]);
    }
}
