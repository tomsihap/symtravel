<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Form\DestinationType;
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
     * @Route("/{id}", name="destination_show", methods={"GET"})
     */
    public function show(Destination $destination)
    {

        return $this->render('destination/show.html.twig', [
            'destination' => $destination
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
}
