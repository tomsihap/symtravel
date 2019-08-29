<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Entity\Review;
use App\Form\DestinationType;
use App\Form\ReviewType;
use App\Repository\DestinationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DestinationController
 * @package App\Controller
 *
 * @Route("/destinations")
 */
class DestinationController extends AbstractController
{
    /**
     * @Route("/", name="destination_index")
     */
    public function index(DestinationRepository $destinationRepository, Request $request)
    {

        $results = null;

        $destinationSearch = $request->request->get('destination_search');

        if ( $destinationSearch ) {

            $results = $destinationRepository->findByNameAndDescription( $destinationSearch );

        }
        else {

            $results = $destinationRepository->findAll();
        }

        return $this->render('destination/index.html.twig', [
            'destinations' => $results
        ]);
    }

    /**
     * @Route("/new", name="destination_create")
     * @Route("/{id}/edit", name="destination_edit")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, Destination $destination = null)
    {

        if ($destination === null)
        {
            $destination = new Destination();
        }

        $form = $this->createForm(DestinationType::class, $destination);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Traitement de la photo

            $photo = $form['photo']->getData();

            if ($photo) {

                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('destinations_photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e);
                }

                $destination->setPhoto($newFilename);
            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($destination);
            $entityManager->flush();

            return $this->redirectToRoute('destination_index');
        }

        return $this->render('destination/create.html.twig', [
            'form' => $form->createView(),
            'destination' => $destination
        ]);
    }


    /**
     * @Route("/{id}", name="destination_show", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function show(Destination $destination, Request $request)
    {

        $userId = $this->getUser()->getId();

        $review = $destination->getReviews()->filter(
            function($review) use( $userId ) {
                return ( $review->getUser()->getId() === $userId );
            }
        )->first();

        if (!$review) {
            $review = new Review();
        }

        $reviewForm = $this->createForm(ReviewType::class, $review);

        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid())
        {

            $review->setUser($this->getUser());
            $review->setDestination($destination);

            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('destination_show', ['id' => $destination->getId()]);

        }

        return $this->render('destination/show.html.twig', [
            'destination'   => $destination,
            'reviewForm'    => $reviewForm->createView(),
            'review'        => $review
        ]);

    }


}
