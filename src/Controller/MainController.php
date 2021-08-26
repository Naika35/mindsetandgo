<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
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
     *@Route("quote/categories", name="categories_list")
     */
    public function categoriesList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('main/categoriesList.html.twig', [
            'categories' => $categories,
        ]);
    }
}
