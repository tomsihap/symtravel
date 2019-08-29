<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(?User $user)
    {
        if ($this->getUser() === $user) {

            return $this->render('user/show.html.twig', [
                'user' => $user
            ]);

        }

        return $this->redirectToRoute('app_home');

    }
}
