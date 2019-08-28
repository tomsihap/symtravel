<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Form\DestinationType;
use App\Repository\DestinationRepository;
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
     */
    public function create(Request $request, Destination $destination = null)
    {

        $isEditMode = true;

        if ($destination === null)
        {
            $destination = new Destination();
            $isEditMode = false;
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
            'isEditMode' => $isEditMode
        ]);
    }
}
