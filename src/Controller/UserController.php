<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/slug", name="read")
     */
    public function index(): Response
    {
        return $this->render('user/read.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
