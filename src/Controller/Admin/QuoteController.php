<?php

namespace App\Controller\Admin;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @Route("/admin/quote", name="admin_quote_browse")
     */
    public function browse(QuoteRepository $quoteRepository): Response
    {
        $quotes = $quoteRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/quote/browse.html.twig', [
            'quotes' => $quotes,
        ]);
    }

    /**
     * @Route("/admin/quote-add", name="admin_quote_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $quote = new Quote();

        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été rajoutée');

            return $this->redirectToRoute('admin_quote_browse');
        }
        return $this->render('admin/quote/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quote-edit/{id}", name="admin_quote_edit")
     */
    public function edit(Request $request, Quote $quote, EntityManagerInterface $em)
    {
        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'La citation a bien été modifiée');

            return $this->redirectToRoute('admin_quote_browse');
        }
        return $this->render('admin/quote/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quote-delete/{id}", name="admin_quote_delete")
     */
    public function delete(Quote $quote, EntityManagerInterface $em)
    {
        $em->remove($quote);
        $em->flush();

        $this->addFlash('success', 'La citation a bien été supprimée.');

        return $this->redirectToRoute('admin_quote_browse');
    }
}
