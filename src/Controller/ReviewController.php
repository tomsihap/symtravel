<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{

    /**
     * @Route("/destinations/{destination}/user/{user}", name="review_visited")
     */
    public function hasVisited(ReviewRepository $reviewRepository, Destination $destination, User $user)
    {

        if ( $this->getUser() === $user )
        {

            $reviewUserDestination = $reviewRepository->findOneBy([
               "user" => $user,
               "destination" => $destination,
            ]);

            if (!$reviewUserDestination)
            {

                $review = new Review();

                $review->setUser($user);
                $review->setDestination($destination);

                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();

            }
        }

        return $this->redirectToRoute('destination_index');

    }
}
