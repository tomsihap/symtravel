<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TypeController
 * @package App\Controller
 *
 * @Route("/type")
 */
class TypeController extends AbstractController
{

    /**
     * @Route("/", name="type_index")
     */
    public function index()
    {
        return $this->render('type/index.html.twig', [
            'controller_name' => 'TypeController',
        ]);
    }

    /**
     * @Route("/new", name="type_create", methods={"POST", "GET"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {

        $type = new Type();

        $form = $this->createForm(TypeType::class, $type);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);

        }

        return $this->render('type/create.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
