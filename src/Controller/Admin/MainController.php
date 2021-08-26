<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function index(): Response
    {
        return $this->render('admin/main/homepage.html.twig', [
            
        ]);
    }
}
