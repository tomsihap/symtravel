<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        return $this->render('public/home.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact()
    {
        return $this->render('public/contact.html.twig');

    }
}
